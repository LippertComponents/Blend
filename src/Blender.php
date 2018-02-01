<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/29/2017
 * Time: 3:33 PM
 */

namespace LCI\Blend;
use League\CLImate\CLImate;
use PHPUnit\Runner\Exception;

class Blender
{
    /** @var  \modx */
    protected $modx;

    protected $climate;

    protected $config = [];
    /** @var boolean|array  */
    protected $blendMigrations = false;

    /** @var  \Tagger */
    protected $tagger;

    protected $resource_id_map = [];

    protected $resource_seek_key_map = [];

    protected $category_map = [];

    /** @var string date('Y_m_d_His') */
    protected $timestamp = '';

    /**
     * Stockpile constructor.
     *
     * @param \modX $modx
     * @param array $config
     */
    public function __construct(&$modx, $config=[])
    {
        $this->modx = $modx;

        $blend_modx_migration_dir = dirname(__DIR__);
        if (isset($config['blend_modx_migration_dir'])) {
            $blend_modx_migration_dir = $config['blend_modx_migration_dir'];
        }

        $this->config = [
            'migration_templates_dir' => __DIR__. '/migration_templates/',
            'migrations_dir' => $blend_modx_migration_dir.'database/migrations/',
            'seeds_dir' => $blend_modx_migration_dir.'database/seeds/',
            'model_dir' => __DIR__.'/xpdo/',
            'extras' => [
                'tagger' => false
            ]
        ];
        $this->config = array_merge($this->config, $config);

        $this->timestamp = date('Y_m_d_His');

        $tagger_path = $this->modx->getOption('tagger.core_path', null, $this->modx->getOption('core_path') . 'components/tagger/') . 'model/tagger/';
        if (is_dir($tagger_path)) {
            $this->config['extras']['tagger'] = true;
            /** @var \Tagger $tagger */
            $this->tagger = $this->modx->getService('tagger', 'Tagger', $tagger_path, []);
        }

        $this->modx->addPackage('blend', $this->config['model_dir']);
        //exit();
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     *
     * @return Blender
     */
    public function setTimestamp(string $timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }


    /**
     * @return string
     */
    public function getSeedsDirectory()
    {
        return $this->config['seeds_dir'];
    }

    /**
     * @return string
     */
    public function getMigrationDirectory()
    {
        return $this->config['migrations_dir'];
    }

    /**
     * @param bool $reload
     * @param string $dir
     * @param int $count
     * @param int $id
     * @param string $name
     *
     * @return array ~ array of \BlendMigrations
     */
    public function getBlendMigrationCollection($reload=false, $dir='ASC', $count=0, $id=0, $name=null)
    {
        if (!$this->blendMigrations || $reload) {
            $blendMigrations = [];

            /** @var \xPDOQuery $query */
            $query = $this->modx->newQuery('BlendMigrations');
            if ($id > 0 ) {
                $query->where(['id' => $id]);
            } elseif (!empty($name)) {
                $query->where(['name' => $name]);
            }
            // @TODO need a ran sequence column to better order of down
            $query->sortBy('name', $dir);
            if ($count > 0 ) {
                $query->limit($count);
            }
            $query->prepare();
            //echo 'SQL: '.$query->toSQL();
            $migrationCollection = $this->modx->getCollection('BlendMigrations', $query);

            /** @var \BlendMigrations $migration */
            foreach ($migrationCollection as $migration) {
                $blendMigrations[$migration->get('name')] = $migration;
            }
            $this->blendMigrations = $blendMigrations;
        }
        return $this->blendMigrations;
    }

    /**
     * @param CLImate $climate
     *
     * @return $this
     */
    public function setClimate(CLImate $climate)
    {
        $this->climate = $climate;
        return $this;
    }

    /**
     * @return \Tagger
     */
    public function getTagger()
    {
        return $this->tagger;
    }

    public function getCategoryMap($refresh=false)
    {
        if (count($this->category_map) == 0 || $refresh) {
            $this->category_map = [
                'ids' => [],
                'names' => [],
                'lineage' => []
            ];
            $query = $this->modx->newQuery('modCategory');
            $query->sortBy('parent');
            $query->sortBy('rank');
            $categories = $this->modx->getCollection('modCategory', $query);
            foreach ($categories as $category) {
                $category_data = $category->toArray();

                $this->category_map['ids'][$category->get('id')] = $category_data;

                $key = trim($category->get('category'));
                // This is not unique!
                $this->category_map['names'][$key] = $category_data;

                // Get the lineage: Parent=>Child=>Grand Child as key
                $lineage = $key;
                if ($category_data['parent'] > 0) {
                    $lineage = $this->category_map['ids'][$category_data['parent']]['lineage'].'=>'.$key;
                }

                $this->category_map['ids'][$category->get('id')]['lineage'] = $lineage;

                $this->category_map['lineage'][$lineage] = $category->toArray();
            }
        }
        return $this->category_map;
    }

    /**
     * Use this method with your IDE to help manually build a Chunk with PHP
     * @param string $name
     * @return Chunk
     */
    public function blendOneRawChunk($name)
    {
        /** @var Chunk $chunk */
        $chunk =  new Chunk($this->modx, $this);
        return $chunk
            ->setName($name)
            ->setSeedTimeDir($this->getTimestamp());
    }
    /**
     * @param array $chunks
     * @param string $timestamp
     */
    public function blendManyChunks($chunks=[], $timestamp='')
    {
        // will update if element does exist or create new
        foreach ($chunks as $seed_key) {
            $blendChunk = new Chunk($this->modx, $this);
            if (!empty($timestamp)) {
                $blendChunk->setSeedTimeDir($timestamp);
            }
            if ($blendChunk->blendFromSeed($seed_key)) {
                $this->out($seed_key.' has been blended into ID: ');

            } elseif($blendChunk->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' chunk already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendChunk->blendFromSeed($seed_key, true)) {
                        $this->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $chunks
     * @param string $timestamp
     */
    public function revertBlendManyChunks($chunks=[], $timestamp='')
    {
        // will update if system setting does exist or create new
        foreach ($chunks as $seed_key) {
            /** @var Chunk $systemSetting */
            $blendChunk = new Chunk($this->modx, $this);
            if (!empty($timestamp)) {
                $blendChunk->setSeedTimeDir($timestamp);
            }

            if ( $blendChunk->revertBlendFromSeed($seed_key) ) {
                $this->out($blendChunk->getName().' chunk has been reverted to '.$timestamp);

            } else {
                $this->out($blendChunk->getName().' chunk was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Plugin with PHP
     * @param string $name
     * @return Plugin
     */
    public function blendOneRawPlugin($name)
    {
        /** @var Plugin $plugin */
        $plugin =  new Plugin($this->modx, $this);
        return $plugin
            ->setName($name)
            ->setSeedTimeDir($this->getTimestamp());
    }

    /**
     * @param array $plugins
     * @param string $timestamp
     */
    public function blendManyPlugins($plugins=[], $timestamp='')
    {
        // will update if element does exist or create new
        foreach ($plugins as $seed_key) {
            $blendPlugin = new Plugin($this->modx, $this);
            if (!empty($timestamp)) {
                $blendPlugin->setSeedTimeDir($timestamp);
            }
            if ($blendPlugin->blendFromSeed($seed_key)) {
                $this->out($seed_key.' has been blended into ID: ');

            } elseif($blendPlugin->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' plugin already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendPlugin->blendFromSeed($seed_key, true)) {
                        $this->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $plugins
     * @param string $timestamp
     */
    public function revertBlendManyPlugins($plugins=[], $timestamp='')
    {
        // will update if system setting does exist or create new
        foreach ($plugins as $seed_key) {
            /** @var Plugin $systemSetting */
            $blendPlugin = new Plugin($this->modx, $this);
            if (!empty($timestamp)) {
                $blendPlugin->setSeedTimeDir($timestamp);
            }

            if ( $blendPlugin->revertBlendFromSeed($seed_key) ) {
                $this->out($blendPlugin->getName().' plugin has been reverted to '.$timestamp);

            } else {
                $this->out($blendPlugin->getName().' plugin was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Snippet with PHP
     * @param string $name
     * @return Snippet
     */
    public function blendOneRawSnippet($name)
    {
        /** @var Snippet $snippet */
        $snippet =  new Snippet($this->modx, $this);
        return $snippet
            ->setName($name)
            ->setSeedTimeDir($this->getTimestamp());
    }

    /**
     * @param array $snippets
     * @param string $timestamp
     */
    public function blendManySnippets($snippets=[], $timestamp='')
    {
        // will update if element does exist or create new
        foreach ($snippets as $seed_key) {
            /** @var Snippet $blendSnippet */
            $blendSnippet = new Snippet($this->modx, $this);
            if (!empty($timestamp)) {
                $blendSnippet->setSeedTimeDir($timestamp);
            }
            if ($blendSnippet->blendFromSeed($seed_key)) {
                $this->out($seed_key.' has been blended');

            } elseif($blendSnippet->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' snippet already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendSnippet->blendFromSeed($seed_key, true)) {
                        $this->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
            }
        }
    }
    /**
     * @param array $snippets
     * @param string $timestamp
     */
    public function revertBlendManySnippets($snippets=[], $timestamp='')
    {
        // will update if system setting does exist or create new
        foreach ($snippets as $seed_key) {
            /** @var Snippet $systemSetting */
            $blendSnippet = new Snippet($this->modx, $this);
            if (!empty($timestamp)) {
                $blendSnippet->setSeedTimeDir($timestamp);
            }

            if ( $blendSnippet->revertBlendFromSeed($seed_key) ) {
                $this->out($blendSnippet->getName().' snippet has been reverted to '.$timestamp);

            } else {
                $this->out($blendSnippet->getName().' snippet was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to manually build a template
     * @param string $name
     * @return Template
     */
    public function blendOneRawTemplate($name)
    {
        /** @var Template $template */
        $template =  new Template($this->modx, $this);
        return $template
            ->setSeedTimeDir($this->timestamp)
            ->setName($name);
    }

    /**
     * @param array $templates
     * @param string $timestamp
     */
    public function blendManyTemplates($templates=[], $timestamp='')
    {
        $blendTemplate = new Template($this->modx, $this);
        if (!empty($timestamp)) {
            $blendTemplate->setSeedTimeDir($timestamp);
        }
        // will update if template does exist or create new
        foreach ($templates as $seed_key) {

            /** @var Snippet $blendTemplate */
            $blendTemplate = new Template($this->modx, $this);
            if (!empty($timestamp)) {
                $blendTemplate->setSeedTimeDir($timestamp);
            }
            if ($blendTemplate->blendFromSeed($seed_key)) {
                $this->out($seed_key.' has been blended');

            } elseif($blendTemplate->isExists()) {
                $this->out($seed_key.' template already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendTemplate->blendFromSeed($seed_key, true)) {
                        $this->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $templates
     * @param string $timestamp
     */
    public function revertBlendManyTemplates($templates=[], $timestamp='')
    {
        // will update if system setting does exist or create new
        foreach ($templates as $seed_key) {
            /** @var Template $blendTemplate */
            $blendTemplate = new Template($this->modx, $this);
            if (!empty($timestamp)) {
                $blendTemplate->setSeedTimeDir($timestamp);
            }

            if ( $blendTemplate->revertBlendFromSeed($seed_key) ) {
                $this->out($blendTemplate->getName().' snippet has been reverted to '.$timestamp);

            } else {
                $this->out($blendTemplate->getName().' snippet was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to manually build a template variable
     * @param string $name
     * @return TemplateVariable
     */
    public function blendOneRawTemplateVariable($name)
    {
        /** @var Element $tv */
        $tv =  new TemplateVariable($this->modx, $this);
        return $tv
            ->setSeedTimeDir($this->timestamp)
            ->setName($name);
    }

    /**
     * @param array $resources
     * @param string $timestamp
     * @param bool $overwrite
     *
     * @return bool
     */
    public function blendManyResources($resources=[], $timestamp='', $overwrite=false)
    {
        $saved = true;
        // will update if resource does exist or create new
        foreach ($resources as $seed_key) {
            /** @var \LCI\Blend\Resource $blendResource */
            $blendResource = new Resource($this->modx, $this);
            if (!empty($timestamp)) {
                $blendResource->setSeedTimeDir($timestamp);
            }

            if ($blendResource->blendFromSeed($seed_key, $overwrite)) {
                $this->out($seed_key.' has been blended into ID: ');

            } elseif($blendResource->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendResource->blendFromSeed($seed_key, true)) {
                        $this->out($seed_key.' has been blended into ID: ');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
                $saved = false;
            }
        }

        return $saved;
    }

    /**
     * @param array $resources
     * @param string $timestamp
     * @param bool $overwrite
     *
     * @return bool
     */
    public function revertBlendManyResources($resources=[], $timestamp='', $overwrite=false)
    {
        $saved = true;
        // will update if resource does exist or create new
        foreach ($resources as $seed_key) {
            /** @var \LCI\Blend\Resource $blendResource */
            $blendResource = new Resource($this->modx, $this);
            if (!empty($timestamp)) {
                $blendResource->setSeedTimeDir($timestamp);
            }

            if ($blendResource->revertBlendFromSeed($seed_key)) {
                $this->out($seed_key.' has been reverted ');

            } else {
                $this->out('There was an error reverting resource '.$seed_key, true);
                $saved = false;
            }
        }

        return $saved;
    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $timestamp
     *
     * @return bool
     */
    public function blendManySystemSettings($settings=[], $timestamp='')
    {
        $success = true;
        // will update if system setting does exist or create new
        foreach ($settings as $setting) {
            $systemSetting = new SystemSetting($this->modx, $this);
            if (!empty($timestamp)) {
                $systemSetting->setSeedTimeDir($timestamp);
            }
            if (isset($setting['key'])) {
                $systemSetting->setName($setting['key']);

            } elseif (isset($setting['name'])) {
                $systemSetting->setName($setting['key']);

            } else {
                // Error: no name/key
                $success = false;
                continue;
            }

            if (isset($setting['namespace'])) {
                $systemSetting->setNamespace($setting['namespace']);
            }
            if (isset($setting['area'])) {
                $systemSetting->setArea($setting['area']);
            }
            if (isset($setting['value'])) {
                $systemSetting->setValue($setting['value']);
            }

            if (isset($setting['xtype'])) {
                $systemSetting->setType($setting['xtype']);

            } elseif (isset($setting['type'])) {
                $systemSetting->setType($setting['type']);
            }

            if ( $systemSetting->blend() ) {
                $this->out($systemSetting->getName().' setting has been blended');
            } else {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $timestamp
     *
     * @return bool
     */
    public function revertBlendManySystemSettings($settings=[], $timestamp='')
    {
        $success = true;
        // will update if system setting does exist or create new
        foreach ($settings as $setting) {
            $systemSetting = new SystemSetting($this->modx, $this);
            if (!empty($timestamp)) {
                $systemSetting->setSeedTimeDir($timestamp);
            }
            if (isset($setting['key'])) {
                $systemSetting->setName($setting['key']);

            } elseif (isset($setting['name'])) {
                $systemSetting->setName($setting['key']);

            } else {
                // Error: no name/key
                $success = false;
                continue;
            }

            if ( $systemSetting->revertBlend() ) {
                $this->out($systemSetting->getName().' setting has been reverted to '.$timestamp);

            } else {
                $this->out($systemSetting->getName().' setting was not reverted', true);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param string $question
     * @param string $default
     *
     * @return mixed
     */
    protected function prompt($question, $default='')
    {
        $input = $this->climate->input($question.' '.(!empty($default) ? "($default)" : ''));
        $input->defaultTo($default);
        return $input->prompt();
    }

    /**
     * @param string $message
     * @param bool $error
     */
    public function out($message, $error=false)
    {
        if ($error) {
            $this->climate->error($message);
        } else {
            $this->climate->out($message);
        }
    }

    /**
     * @param string $name
     * @param string $server_type
     *
     * @return bool
     */
    public function createBlankMigrationClassFile($name, $server_type='master')
    {
        return $this->writeMigrationClassFile('blank', [], $server_type, $name);
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     * @param bool $create_migration_file
     *
     * @return array
     */
    public function makeChunkSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modChunk', $criteria);

        foreach ($collection as $chunk) {
            /** @var Chunk $blendChunk */
            $blendChunk = new Chunk($this->modx, $this);
            $seed_key = $blendChunk
                ->setSeedTimeDir($this->timestamp)
                ->seedElement($chunk);
            $this->out("Chunk: ".$chunk->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            $this->writeMigrationClassFile('chunk', $keys, $server_type, $name);
        }
        return $keys;
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     * @param bool $create_migration_file
     *
     * @return array
     */
    public function makePluginSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modPlugin', $criteria);

        foreach ($collection as $plugin) {
            /** @var Plugin $blendPlugin */
            $blendPlugin = new Plugin($this->modx, $this);
            $seed_key = $blendPlugin
                ->setSeedTimeDir($this->timestamp)
                ->seedElement($plugin);
            $this->out("Plugin: ".$plugin->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            $this->writeMigrationClassFile('plugin', $keys, $server_type, $name);
        }
        return $keys;
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     * @param bool $create_migration_file
     *
     * @return array
     */
    public function makeResourceSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $keys = [];

        $collection = $this->modx->getCollection('modResource', $criteria);
        foreach ($collection as $resource) {
            $blendResource = new Resource($this->modx, $this);
            $seed_key = $blendResource
                ->setSeedTimeDir($this->timestamp)
                ->seed($resource);
            $this->out("ID: ".$resource->get('id').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            $this->writeMigrationClassFile('resource', $keys, $server_type, $name);
        }
        return $keys;
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     * @param bool $create_migration_file
     *
     * @return array
     */
    public function makeSnippetSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modSnippet', $criteria);

        foreach ($collection as $snippet) {
            /** @var Snippet $blendSnippet */
            $blendSnippet = new Snippet($this->modx, $this);
            $seed_key = $blendSnippet
                ->setSeedTimeDir($this->timestamp)
                ->seedElement($snippet);
            $this->out("Snippet: ".$snippet->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if($create_migration_file) {
            $this->writeMigrationClassFile('snippet', $keys, $server_type, $name);
        }
        return $keys;
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     * @param bool $create_migration_file
     *
     * @return array
     */
    public function makeSystemSettingSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $collection = $this->modx->getCollection('modSystemSetting', $criteria);

        $setting_data = [];
        foreach ($collection as $setting) {
            // @TODO transform all values that are IDs, templates, resources, ect.
            $setting_data[] = $setting->toArray();
        }

        // https://docs.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.invokeevent
        $this->modx->invokeEvent(
            'OnBlendSeedSystemSettings',
            [
                'blender' => $this,
                'data' => &$setting_data
            ]
        );
        if ($create_migration_file) {
            $this->writeMigrationClassFile('systemSettings', $setting_data, $server_type, $name);
        }
        return $setting_data;
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     * @param bool $create_migration_file
     *
     * @return array
     */
    public function makeTemplateSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modTemplate', $criteria);

        foreach ($collection as $template) {
            $blendTemplate = new Template($this->modx, $this);
            $seed_key = $blendTemplate
                ->setSeedTimeDir($this->timestamp)
                ->seedElement($template);
            $this->out("Template ID: ".$template->get('id').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }
        if ($create_migration_file) {
            $this->writeMigrationClassFile('template', $keys, $server_type, $name);
        }
        return $keys;
    }

    /**
     * @param string $server_type
     * @param null|string $name
     */
    public function makeSiteSeed($server_type='master', $name=null)
    {
        $site_data = [
            'chunks' => $this->makeChunkSeeds(null, $server_type, null, false),
            'plugins' => $this->makePluginSeeds(null, $server_type, null, false),
            'resources' => $this->makeResourceSeeds(null, $server_type, null, false),
            'snippets' => $this->makeSnippetSeeds(null, $server_type, null, false),
            'systemSettings' => $this->makeSystemSettingSeeds(null, $server_type, null, false),
            'templates' => $this->makeTemplateSeeds(null, $server_type, null, false)
        ];

        $this->writeMigrationClassFile('site', $site_data, $server_type, $name);
    }

    /**
     * @param string $method
     */
    public function install($method='up', $prompt=false)
    {
        $name = 'install_blender';

        // new blender for each instance
        $config = $this->config;
        $config['migrations_dir'] = __DIR__.'/migration/';

        $blender = new Blender($this->modx, $config);
        $blender->setClimate($this->climate);

        /** @var Migrations $migrationProcessClass */
        $migrationProcessClass = $this->loadMigrationClass($name, $blender);

        if (!$migrationProcessClass instanceof Migrations) {
            $this->out('File is not an instance of LCI\Blend\Migrations: '.$name, true);
            $this->out('Did not process, verify it is in the proper directory', true);

        } elseif ($method == 'up' && !$this->isBlendInstalledInModx()) {

            $migrationProcessClass->up();

            /** @var \BlendMigrations $migration */
            $migration = $this->modx->newObject('BlendMigrations');
            if ($migration) {
                $this->out('Blender loaded', true);
                $migration->set('name', $name);
                $migration->set('type', 'master');
                $migration->set('description', $migrationProcessClass->getDescription());
                $migration->set('version', $migrationProcessClass->getVersion());
                $migration->set('status', 'up_complete');
                $migration->set('created_at', date('Y-m-d H:i:s'));
                $migration->set('processed_at', date('Y-m-d H:i:s'));
                $migration->save();

                // does the migration directory exist?
                if (!file_exists($this->getMigrationDirectory())) {
                    $create = true;
                    if ($prompt) {
                        $response = $this->prompt('Create the following directory for migration files? (y/n) '.PHP_EOL
                            .$this->getMigrationDirectory(), 'y');
                        if (strtolower(trim($response)) != 'y') {
                            $create = false;
                        }
                    }
                    if ($create) {
                        mkdir($this->getMigrationDirectory(), 0700, true);
                        $this->out('Created migration directory: '. $this->getMigrationDirectory());
                    }
                }

            } else {
                $this->out('Blender did not save the DB correctly ', true);
            }

        } elseif ($method == 'down') {
            $migrationProcessClass->down();

            /** @var \BlendMigrations $migration */
            $migration = $this->modx->getObject('BlendMigrations', ['name' => $name]);
            if ($migration) {
                $migration->set('name', $name);
                $migration->set('description', $migrationProcessClass->getDescription());
                $migration->set('version', $migrationProcessClass->getVersion());
                $migration->set('status', 'down_complete');
                $migration->set('processed_at', date('Y-m-d H:i:s'));
                $migration->save();
            }

        }
    }

    /**
     * @return bool
     */
    public function isBlendInstalledInModx()
    {
        try {
            $table = $this->modx->getTableName('BlendMigrations');
            if ($this->modx->query("SELECT 1 FROM {$table} LIMIT 1") === false) {
                return false;
            }
        } catch (Exception $exception) {
            // We got an exception == table not found
            return false;
        }

        /** @var \xPDOQuery $query */
        $query = $this->modx->newQuery('BlendMigrations');
        $query->sortBy('name');

        $installMigration = $this->modx->getObject('BlendMigrations', [
            'name' => 'install_blender',
            'status' => 'up_complete'
        ]);
        if ($installMigration instanceof \BlendMigrations) {
            return true;
        }

        return false;
    }
    /**
     * @param string $method
     * @param string $type
     * @param int $count
     * @param int $id
     * @param string $name
     */
    public function runMigration($method='up', $type='master', $count=0, $id=0, $name=null)
    {
        $dir = 'ASC';
        if ($method == 'down') {
            $dir = 'DESC';
        } else {
            $count = 0;
        }
        // 1. Get all migrations currently in DB:
        $blendMigrations = $this->getBlendMigrationCollection(false, $dir, $count, $id, $name);

        // 2. Load migration files:
        if ($method == 'up') {
            if ($this->retrieveMigrationFiles()) {
                // this is needed just to insure that the order is correct and any new files
                $blendMigrations = $this->getBlendMigrationCollection(true, $dir, $count, $id, $name);
            }
        }

        // 3. now run migration if proper
        /** @var \BlendMigrations $migration */
        foreach ($blendMigrations as $name => $migration) {
            if ($id > 0 && $migration->get('id') != $id) {
                continue;
            }
            /** @var string $name */
            $name = $migration->get('name');

            /** @var string $status ~ ready|up_complete|down_complete*/
            $status = $migration->get('status');

            /** @var string $server_type */
            $server_type = $migration->get('type');

            if ( ($server_type != $type) || ($method == 'up' && $status == 'up_complete') || ($method == 'down' && $status != 'up_complete') ) {
                continue;
            }

            // new blender for each instance
            $blender = new Blender($this->modx, $this->config);
            $blender->setClimate($this->climate);

            /** @var Migrations $migrationProcessClass */
            $migrationProcessClass = $this->loadMigrationClass($name, $blender);

            if ($migrationProcessClass instanceof Migrations) {
                $this->out('Load Class: '.$name.' M: '.$method);
                if ($method == 'up') {
                    $migrationProcessClass->up();
                    $this->out('Run up: '.$name);
                    $migration->set('status', 'up_complete');
                    $migration->set('processed_at', date('Y-m-d H:i:s'));
                    $migration->save();

                } elseif ($method == 'down') {
                    $migrationProcessClass->down();
                    $migration->set('status', 'down_complete');
                    $migration->set('processed_at', date('Y-m-d H:i:s'));
                    $migration->save();

                } else {
                    // error
                }
            } else {
                // error
            }
        }
    }

    /**
     * @return bool ~ true if new migrations were found
     */
    public function retrieveMigrationFiles()
    {
        // 1. Get all migrations currently in DB:
        $migrationCollection = $this->modx->getCollection('BlendMigrations');

        $blendMigrations = [];

        /** @var \BlendMigrations $migration */
        foreach ($migrationCollection as $migration) {
            $blendMigrations[$migration->get('name')] = $migration;
        }

        $migration_dir = $this->getMigrationDirectory();
        $this->climate->out('Searching '.$migration_dir);

        $reload = false;
        /** @var \DirectoryIterator $file */
        foreach (new \DirectoryIterator($this->getMigrationDirectory()) as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {

                $name = $file->getBasename('.php');
                // @TODO query DB! and test this method
                if (!isset($blendMigrations[$name])) {
                    $this->out('Create new '.$name);
                    /** @var Migrations $migrationProcessClass */
                    $migrationProcessClass = $this->loadMigrationClass($name, $this);

                    /** @var \BlendMigrations $migration */
                    $migration = $this->modx->newObject('BlendMigrations');
                    $migration->set('name', $name);
                    $migration->set('status', 'ready');
                    if ($migrationProcessClass instanceof Migrations) {
                        $migration->set('description', $migrationProcessClass->getDescription());
                        $migration->set('version', $migrationProcessClass->getVersion());
                    }
                    if (!$migration->save()) {
                        exit();
                    };

                    $reload = true;
                }
            }
        }
        return $reload;
    }

    /**
     * @param string $name
     * @param Blender $blender
     *
     * @return bool|Migrations
     */
    protected function loadMigrationClass($name, Blender $blender)
    {
        $migrationProcessClass = false;

        $file = $blender->getMigrationDirectory().$name.'.php';
        if (file_exists($file)) {
            require_once $file;

            if(class_exists($name)) {
                /** @var Migrations $migrationProcessClass */
                $migrationProcessClass = new $name($this->modx, $blender);
            }
        }

        return $migrationProcessClass;
    }
    /**
     * @param string $type
     *
     * @return string
     */
    protected function getMigrationName($type)
    {
        return 'm'.$this->timestamp.'_'.ucfirst(strtolower($type));
    }

    /**
     * @param string $type
     * @param array $class_data
     * @param string $server_type
     * @param string $name
     * @param bool $log
     *
     * @return bool
     */
    protected function writeMigrationClassFile($type, $class_data=[], $server_type='master', $name=null, $log=true)
    {
        if (!empty($name)) {
            $class_name = $name = preg_replace('/[^A-Za-z0-9\_\.]/', '', str_replace(['/', ' '], '_', $name));
        } else {
            $class_name = $this->getMigrationName($type);
        }

        $migration_template = 'blank.txt';
        $placeholders = [
            'classCreateDate' => date('Y/m/d'),
            'classCreateTime' => date('G:i:s T P'),
            'className' => $class_name,
            'classUpInners' => '//@TODO',
            'classDownInners' => '//@TODO',
            'serverType' => $server_type,
            'timestamp' => $this->timestamp
        ];

        switch ($type) {

            case 'chunk':
                $migration_template = 'chunk.txt';
                $placeholders['chunkData'] = $this->prettyVarExport($class_data);
                $placeholders['classUpInners'] = '$this->blender->blendManyChunks($this->chunks, $this->getTimestamp());';
                $placeholders['classDownInners'] = '$this->blender->revertBlendManyChunks($this->chunks, $this->getTimestamp());';
                break;

            case 'plugin':
                $migration_template = 'plugin.txt';
                $placeholders['pluginData'] = $this->prettyVarExport($class_data);
                $placeholders['classUpInners'] = '$this->blender->blendManyPlugins($this->plugins, $this->getTimestamp());';
                $placeholders['classDownInners'] = '$this->blender->revertBlendManyPlugins($this->plugins, $this->getTimestamp());';
                break;

            case 'resource':
                $migration_template = 'resource.txt';
                $placeholders['resourceData'] = $this->prettyVarExport($class_data);
                $placeholders['classUpInners'] = '$this->blender->blendManyResources($this->resources, $this->getTimestamp());';
                $placeholders['classDownInners'] = '$this->blender->revertBlendManyResources($this->resources, $this->getTimestamp());';
                break;

            case 'snippet':
                $migration_template = 'snippet.txt';
                $placeholders['snippetData'] = $this->prettyVarExport($class_data);
                $placeholders['classUpInners'] = '$this->blender->blendManySnippets($this->snippets, $this->getTimestamp());';
                $placeholders['classDownInners'] = '$this->blender->revertBlendManySnippets($this->snippets, $this->getTimestamp());';
                break;

            case 'systemSettings':
                $migration_template = 'systemSettings.txt';
                $placeholders['settingsData'] = $this->prettyVarExport($class_data);
                $placeholders['classUpInners'] = '$this->blender->blendManySystemSettings($this->settings, $this->getTimestamp());';
                $placeholders['classDownInners'] = '$this->blender->revertBlendManySystemSettings($this->settings, $this->getTimestamp());';
                break;

            case 'template':
                $migration_template = 'template.txt';
                $placeholders['templateData'] = $this->prettyVarExport($class_data);
                $placeholders['classUpInners'] = '$this->blender->blendManyTemplates($this->templates, $this->getTimestamp());';
                $placeholders['classDownInners'] = '$this->blender->revertBlendManyTemplates($this->templates, $this->getTimestamp());';
                break;


            case 'site':
                $migration_template = 'site.txt';
                $placeholders['siteData'] = $this->prettyVarExport($class_data);
                break;
        }

        $file_contents = '';

        $migration_template = $this->config['migration_templates_dir'].$migration_template;
        if (file_exists($migration_template)) {
            $file_contents = file_get_contents($migration_template);
        }

        foreach ($placeholders as $name => $value) {
            $file_contents = str_replace('[[+'.$name.']]', $value, $file_contents);
        }

        $this->out($this->getMigrationDirectory().$class_name.'.php');

        $write = false;
        if (file_exists($this->getMigrationDirectory().$class_name.'.php')) {
            $this->out($this->getMigrationDirectory() . $class_name . '.php migration file already exists', true);

        } elseif (is_object($this->modx->getObject('BlendMigrations', ['name' => $class_name]))) {
            $this->out($class_name . ' migration already has been created in the blend_migrations table', true);

        } else {
            try {
                $write = file_put_contents($this->getMigrationDirectory() . $class_name . '.php', $file_contents);
                $migration = $this->modx->newObject('BlendMigrations');
                if ($migration && $log) {
                    $this->out('Blender loaded', true);
                    $migration->set('name', $class_name);
                    $migration->set('type', 'master');
                    $migration->set('description', '');// @TODO
                    $migration->set('version', '');
                    $migration->set('status', 'seed export');
                    $migration->set('created_at', date('Y-m-d H:i:s'));
                    $migration->save();
                }
            } catch (Exception $exception) {
                $this->out($exception->getMessage(), true);
            }
            if (!$write) {
                $this->out($this->getMigrationDirectory() . $class_name . '.php Did not write to file', true);
                $this->out('Verify that the folders exists and are writable by PHP', true);
            }
        }

        return $write;
    }

    /**
     * @param string $name
     * @param string $type ~ chunk, plugin, resource, snippet, systemSettings, template, site
     *
     * @return bool
     */
    public function removeMigrationFile($name, $type)
    {
        if (!empty($name)) {
            $class_name = $name = preg_replace('/[^A-Za-z0-9\_\.]/', '', str_replace(['/', ' '], '_', $name));
        } else {
            $class_name = $this->getMigrationName($type);
        }

        $removed = false;
        $migration_file = $this->getMigrationDirectory() . $class_name . '.php';
        if (file_exists($migration_file)) {
            if (unlink($migration_file)) {
                $removed = true;
                $migration = $this->modx->getObject('BlendMigrations', ['name' => $class_name]);
                if (is_object($migration) && $migration->remove()) {
                    $this->out($class_name . ' migration has been removed from the blend_migrations table');

                }
            } else {
                $this->out($class_name . ' migration has not been removed from the blend_migrations table', true);
            }

        } else {
            $this->out($this->getMigrationDirectory() . $class_name . '.php migration could not be found to remove', true);
        }

        return $removed;
    }
    /**
     * @param int $id
     *
     * @return bool|string
     */
    public function getResourceSeedKeyFromID($id)
    {
        if (!isset($this->resource_id_map[$id])) {
            $seed_key = false;
            $resource = $this->modx->getObject('modResource', $id);
            if ($resource) {
                $seed_key = $this->getSeedKeyFromAlias($resource->get('alias'));
                $this->resource_seek_key_map[$seed_key] = $id;
            }
            $this->resource_id_map[$id] = $seed_key;
        }

        return $this->resource_id_map[$id];
    }

    /**
     * @param string $seed_key
     *
     * @return bool|int
     */
    public function getResourceIDFromSeedKey($seed_key)
    {
        if (!isset($this->resource_seek_key_map[$seed_key])) {
            $id = false;
            $alias = $this->getAliasFromSeedKey($seed_key);
            $resource = $this->modx->getObject('modResource', ['alias' => $alias]);
            if ($resource) {
                $id = $resource->get('id');
                $this->resource_seek_key_map[$seed_key] = $id;
                $this->resource_id_map[$id] = $seed_key;
            }
            $this->resource_seek_key_map[$seed_key] = $id;
        }

        return $this->resource_seek_key_map[$seed_key];
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    public function getSeedKeyFromAlias($alias)
    {
        return str_replace('/', '#', $alias);
    }

    /**
     * @param string $seed_key
     *
     * @return string
     */
    public function getAliasFromSeedKey($seed_key)
    {
        return str_replace('#', '/', $seed_key);
    }

    /**
     * @param string $name
     * @param string $type ~ template, template-variable, chunk, snippet or plugin
     * @return string
     */
    public function getElementSeedKeyFromName($name, $type)
    {
        return $type.'_'.str_replace('/', '#', $name);
    }

    /**
     * @param mixed|array $data
     * @param int $tabs
     *
     * @return string
     */
    protected function prettyVarExport($data, $tabs=1)
    {
        $spacing = str_repeat(' ', 4*$tabs);

        $string = '';
        $parts = preg_split('/\R/', var_export($data, true));
        foreach ($parts as $k => $part) {
            if ($k > 0) {
                $string .= $spacing;
            }
            $string .= $part.PHP_EOL;
        }

        return trim($string);
    }
}
/**
 * id | element_class | name | data | action ??
 */
