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
     *
     * @return array ~ array of \BlendMigrations
     */
    public function getBlendMigrationCollection($reload=false, $dir='ASC', $count=0, $id=0)
    {
        if (!$this->blendMigrations || $reload) {
            $blendMigrations = [];

            /** @var \xPDOQuery $query */
            $query = $this->modx->newQuery('BlendMigrations');
            if ($id > 0 ) {
                $query->where(['id' => $id]);
            }
            // @TODO need a ran sequence column to better order of down
            $query->sortBy('name', $dir);
            if ($count > 0 ) {
                $query->limit($count);
            }
            $query->prepare();
            echo 'SQL: '.$query->toSQL();
            $migrationCollection = $this->modx->getCollection('BlendMigrations');

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
                'names' => []
            ];
            $query = $this->modx->newQuery('modCategory');
            $query->sortBy('parent');
            $query->sortBy('rank');
            $categories = $this->modx->getCollection('modCategory', $query);
            foreach ($categories as $category) {
                $this->category_map['ids'][$category->get('id')] = $category->toArray();
                // @TODO this is not unique! Need: Parent=>Child=>Grand Child
                $key = $category->get('name');
                $this->category_map['names'][$key] = $category->toArray();
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
            ->setName($name);
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
            if ($blendChunk->blend($seed_key)) {
                $this->out($seed_key.' has been blended into ID: ');

            } elseif($blendChunk->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' chunk already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendChunk->blend($seed_key, true)) {
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

            if ( $blendChunk->revertBlend($seed_key) ) {
                $this->out($blendChunk->getName().' chunk has been reverted to '.$timestamp);

            } else {
                $this->out($blendChunk->getName().' chunk was not reverted', true);
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
        /** @var Element $snippet */
        $snippet =  new Snippet($this->modx, $this);
        return $snippet
            ->setName($name);
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
            ->setName($name);
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
            if ($blendPlugin->blend($seed_key)) {
                $this->out($seed_key.' has been blended into ID: ');

            } elseif($blendPlugin->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' plugin already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendPlugin->blend($seed_key, true)) {
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
            /** @var Chunk $systemSetting */
            $blendPlugin = new Plugin($this->modx, $this);
            if (!empty($timestamp)) {
                $blendPlugin->setSeedTimeDir($timestamp);
            }

            if ( $blendPlugin->revertBlend($seed_key) ) {
                $this->out($blendPlugin->getName().' plugin has been reverted to '.$timestamp);

            } else {
                $this->out($blendPlugin->getName().' plugin was not reverted', true);
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
            if ($blendTemplate->blendTemplate($seed_key)) {
                $this->out($seed_key.' has been blended into ID: ');

            } elseif($blendTemplate->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' template already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendTemplate->blendTemplate($seed_key, true)) {
                        // @TODO get ID
                        $this->out($seed_key.' has been blended into ID: ');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
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
     */
    public function blendManyResources($resources=[], $timestamp='')
    {
        $blendResource = new Resource($this->modx, $this);
        if (!empty($timestamp)) {
            $blendResource->setSeedTimeDir($timestamp);
        }

        // will update if resource does exist or create new
        foreach ($resources as $seed_key) {
            if ($blendResource->blendResource($seed_key)) {
                $this->out($seed_key.' has been blended into ID: ');
            } elseif($blendResource->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendResource->blendResource($seed_key, true)) {
                        $this->out($seed_key.' has been blended into ID: ');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
            }
        }

    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $timestamp
     */
    public function blendManySystemSettings($settings=[], $timestamp='')
    {
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
            }
        }
    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $timestamp
     */
    public function revertBlendManySystemSettings($settings=[], $timestamp='')
    {
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
                continue;
            }

            if ( $systemSetting->revertBlend() ) {
                $this->out($systemSetting->getName().' setting has been reverted to '.$timestamp);

            } else {
                $this->out($systemSetting->getName().' setting was not reverted', true);
            }
        }
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
     */
    public function createBlankMigrationClassFile($name, $server_type='master')
    {
        $this->writeMigrationClassFile('blank', [], $server_type, $name);
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     */
    public function makeChunkSeeds($criteria, $server_type='master', $name=null)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modChunk', $criteria);

        foreach ($collection as $chunk) {
            /** @var Chunk $blendChunk */
            $blendChunk = new Chunk($this->modx, $this);
            $seed_key = $blendChunk
                ->setSeedTimeDir($this->timestamp)
                ->seedElement($chunk);
            $this->out("Chunk ID: ".$chunk->get('id').' Key: '.$seed_key);
            $keys[] = $seed_key;

        }

        $this->writeMigrationClassFile('chunk', $keys, $server_type, $name);
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     */
    public function makePluginSeeds($criteria, $server_type='master', $name=null)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modPlugin', $criteria);

        foreach ($collection as $plugin) {
            /** @var Plugin $blendPlugin */
            $blendPlugin = new Plugin($this->modx, $this);
            $seed_key = $blendPlugin
                ->setSeedTimeDir($this->timestamp)
                ->seedElement($plugin);
            $this->out("Plugin ID: ".$plugin->get('id').' Key: '.$seed_key);
            $keys[] = $seed_key;

        }

        $this->writeMigrationClassFile('plugin', $keys, $server_type, $name);
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     */
    public function makeResourceSeeds($criteria, $server_type='master', $name=null)
    {
        $keys = [];

        $collection = $this->modx->getCollection('modResource', $criteria);
        foreach ($collection as $resource) {
            $blendResource = new Resource($this->modx, $this);
            $seed_key = $blendResource
                ->setSeedTimeDir($this->timestamp)
                ->seedResource($resource);
            $this->out("ID: ".$resource->get('id').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        $this->writeMigrationClassFile('resource', $keys, $server_type, $name);
        //$this->out($this->getMigrationName('resource'));
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     */
    public function makeSystemSettingSeeds($criteria, $server_type='master', $name=null)
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

        $this->writeMigrationClassFile('systemSettings', $setting_data, $server_type, $name);
        //$this->out($this->getMigrationName('resource'));
    }

    /**
     * @param \xPDOQuery|array|null $criteria
     * @param string $server_type
     * @param string $name
     */
    public function makeTemplateSeeds($criteria, $server_type='master', $name=null)
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

        $this->writeMigrationClassFile('template', $keys, $server_type, $name);
        //$this->out($this->getMigrationName('template'));
    }

    /**
     * @param string $method
     */
    public function install($method='up')
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

        } elseif ($method == 'up') {
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
     */
    public function runMigration($method='up', $type='master', $count=0, $id=0)
    {
        $dir = 'ASC';
        if ($method == 'down') {
            $dir = 'DESC';
        } else {
            $count = 0;
        }
        // 1. Get all migrations currently in DB:
        $blendMigrations = $this->getBlendMigrationCollection(false, $dir, $count, $id);

        // 2. Load migration files:
        if ($method == 'up') {
            if ($this->retrieveMigrationFiles($blendMigrations)) {
                // this is needed just to insure that the order is correct and any new files
                $blendMigrations = $this->getBlendMigrationCollection(true);
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
                $this->out('Load Class: '.$name);
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
     * @param array $blendMigrations
     * @return bool
     */
    protected function retrieveMigrationFiles($blendMigrations)
    {
        $migration_dir = $this->getMigrationDirectory();
        $this->climate->out('Searching '.$migration_dir);

        $reload = false;
        /** @var \DirectoryIterator $file */
        foreach (new \DirectoryIterator($this->getMigrationDirectory()) as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {

                $name = $file->getBasename('.php');
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
                    $migration->save();

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
     */
    protected function writeMigrationClassFile($type, $class_data=[], $server_type='master', $name=null)
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
                $placeholders['classDownInners'] = '//@TODO';
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
                $placeholders['classDownInners'] = '//@TODO';
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

        try {
            $write = file_put_contents($this->getMigrationDirectory().$class_name.'.php', $file_contents);
        } catch (Exception $exception) {
            $write = false;
            $this->out($exception->getMessage(), true);
        }
        if(!$write) {
            $this->out($this->getMigrationDirectory().$class_name.'.php Did not write to file', true);
            $this->out('Verify that the folders exists and are writable by PHP', true);
        }
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
