<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/29/2017
 * Time: 3:33 PM
 */

namespace LCI\Blend;

use LCI\Blend\Helpers\Format;
use LCI\Blend\Migrations\MigrationsCreator;
use LCI\Blend\Model\xPDO\BlendMigrations;
use modX;
use LCI\Blend\Blendable\Chunk;
use LCI\Blend\Blendable\Context;
use LCI\Blend\Blendable\MediaSource;
use LCI\Blend\Blendable\Plugin;
use LCI\Blend\Blendable\Resource;
use LCI\Blend\Blendable\Snippet;
use LCI\Blend\Blendable\SystemSetting;
use LCI\Blend\Blendable\Template;
use LCI\Blend\Blendable\TemplateVariable;
use LCI\Blend\Helpers\SimpleCache;
use LCI\MODX\Console\Helpers\UserInteractionHandler;
use PHPUnit\Runner\Exception;

class Blender
{
    /** @var string ~ version number of the project */
    private $version = '1.0.0 beta';

    /** @var array a list of valid upgrade migrations */
    protected $update_migrations = [
        '0.9.7' => 'v0_9_7_update',
        '0.9.8' => 'v0_9_8_update',
        '0.9.9' => 'v0_9_9_update',
        '0.9.10' => 'v0_9_10_update',
        '0.9.11' => 'v0_9_11_update',
        '1.0.0 beta' => 'v1_0_0_beta_update'
    ];

    /** @var  \modx */
    protected $modx;

    /** @var array  */
    protected $modx_version_info = [];

    /** @var \LCI\MODX\Console\Helpers\UserInteractionHandler */
    protected $userInteractionHandler;

    /** @var array  */
    protected $config = [];

    /** @var boolean|array  */
    protected $blendMigrations = false;

    /** @var  \Tagger */
    protected $tagger;

    protected $resource_id_map = [];

    protected $resource_seek_key_map = [];

    protected $category_map = [];

    /** @var string date('Y_m_d_His') */
    protected $seeds_dir = '';

    /** @var int  */
    protected $xpdo_version = 3;

    protected $blend_class_object = 'BlendMigrations';

    protected $blend_package = 'blend';

    /**
     * Stockpile constructor.
     *
     * @param \modX $modx
     * @param UserInteractionHandler $userInteractionHandler
     * @param array $config
     */
    public function __construct(modX $modx, UserInteractionHandler $userInteractionHandler, $config=[])
    {
        $this->modx = $modx;

        $this->modx_version_info = $this->modx->getVersionData();

        $this->userInteractionHandler = $userInteractionHandler;

        if (version_compare($this->modx_version_info['full_version'], '3.0') >= 0 ) {
            $this->xpdo_version = 3;
            $this->blend_class_object = 'LCI\\Blend\\Model\\xPDO\\BlendMigrations';
            $this->blend_package = 'LCI\\Blend\\Model\\xPDO';

        } else {
            $this->xpdo_version = 2;
        }

        $blend_modx_migration_dir = dirname(__DIR__);
        if (isset($config['blend_modx_migration_dir'])) {
            $blend_modx_migration_dir = $config['blend_modx_migration_dir'];
        }

        $this->config = [
            'migration_templates_path' => __DIR__. '/Migrations/templates/',
            'migrations_path' => $blend_modx_migration_dir.'database/migrations/',
            'seeds_path' => $blend_modx_migration_dir.'database/seeds/',
            'model_dir' => __DIR__ . ($this->xpdo_version >= 3 ? '/' : '/xpdo2/'),
            'extras' => [
                'tagger' => false
            ]
        ];
        $this->config = array_merge($this->config, $config);

        $this->seeds_dir = date('Y_m_d_His');

        $tagger_path = $this->modx->getOption('tagger.core_path', null, $this->modx->getOption('core_path') . 'components/tagger/') . 'model/tagger/';
        if (is_dir($tagger_path)) {
            $this->config['extras']['tagger'] = true;
            /** @var \Tagger $tagger */
            $this->tagger = $this->modx->getService('tagger', 'Tagger', $tagger_path, []);
        }

        if ($this->xpdo_version >= 3) {
            $this->modx->setPackage($this->blend_package, $this->config['model_dir']);

        } else {
            $this->modx->addPackage($this->blend_package, $this->config['model_dir']);
        }
    }

    /**
     * @return string
     */
    public function getBlendClassObject()
    {
        return $this->blend_class_object;
    }

    /**
     * @return UserInteractionHandler
     */
    public function getUserInteractionHandler()
    {
        return $this->userInteractionHandler;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getSeedsDir()
    {
        return $this->seeds_dir;
    }

    /**
     * @param string $seeds_dir ~ local folder
     *
     * @return Blender
     */
    public function setSeedsDir($seeds_dir)
    {
        $this->seeds_dir = $seeds_dir;
        return $this;
    }

    /**
     * @param null $directory_key
     * @return string
     */
    public function getSeedsPath($directory_key=null)
    {
        $seed_path = $this->config['seeds_path'];
        if (!empty($directory_key)) {
            $seed_path .= trim($directory_key, '/') . DIRECTORY_SEPARATOR;
        }
        return $seed_path;
    }

    /**
     * @return string
     */
    public function getMigrationPath()
    {
        return $this->config['migrations_path'];
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
            $query = $this->modx->newQuery($this->blend_class_object);
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
            $migrationCollection = $this->modx->getCollection($this->blend_class_object, $query);

            /** @var \BlendMigrations $migration */
            foreach ($migrationCollection as $migration) {
                $blendMigrations[$migration->get('name')] = $migration;
            }
            $this->blendMigrations = $blendMigrations;
        }
        return $this->blendMigrations;
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
                if ($category_data['parent'] > 0 && isset($this->category_map['ids'][$category_data['parent']]) && isset($this->category_map['ids'][$category_data['parent']]['lineage'])) {
                    $lineage = $this->category_map['ids'][$category_data['parent']]['lineage'].'=>'.$key;
                } elseif ($category_data['parent'] > 0) {
                    //$this->out('DID NOT FIND PARENT?? '. print_r($category_data, true), true);
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
    public function getBlendableChunk($name)
    {
        /** @var \LCI\Blend\Blendable\Chunk $chunk */
        $chunk =  new Chunk($this->modx, $this, $name);
        return $chunk->setSeedsDir($this->getSeedsDir());
    }
    /**
     * @param array $chunks
     * @param string $seeds_dir
     */
    public function blendManyChunks($chunks=[], $seeds_dir='')
    {
        // will update if element does exist or create new
        foreach ($chunks as $seed_key) {
            /** @var \LCI\Blend\Blendable\Chunk $blendChunk */
            $blendChunk = new Chunk($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendChunk->setSeedsDir($seeds_dir);
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
     * @param string $seeds_dir
     */
    public function revertBlendManyChunks($chunks=[], $seeds_dir='')
    {
        // will update if system setting does exist or create new
        foreach ($chunks as $seed_key) {
            /** @var \LCI\Blend\Blendable\Chunk $blendChunk */
            $blendChunk = new Chunk($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendChunk->setSeedsDir($seeds_dir);
            }

            if ( $blendChunk->revertBlend() ) {
                $this->out($blendChunk->getFieldName().' chunk has been reverted to '.$seeds_dir);

            } else {
                $this->out($blendChunk->getFieldName().' chunk was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Chunk with PHP
     * @param string $key
     * @return Context
     */
    public function getBlendableContext($key)
    {
        /** @var \LCI\Blend\Blendable\Context $chunk */
        $context =  new Context($this->modx, $this, $key);
        return $context->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @param array $contexts
     * @param string $seeds_dir
     */
    public function blendManyContexts($contexts=[], $seeds_dir='')
    {
        // will update if element does exist or create new
        foreach ($contexts as $seed_key) {
            /** @var \LCI\Blend\Blendable\Context $blendContext */
            $blendContext = new Context($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendContext->setSeedsDir($seeds_dir);
            }
            if ($blendContext->blendFromSeed($seed_key)) {
                $this->out($seed_key.' has been blended ');

            } elseif($blendContext->isExists()) {
                // @TODO prompt Do you want to blend Y/N/Compare
                $this->out($seed_key.' chunk already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendContext->blendFromSeed($seed_key, true)) {
                        $this->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $contexts
     * @param string $seeds_dir
     */
    public function revertBlendManyContexts($contexts=[], $seeds_dir='')
    {
        // will update if system setting does exist or create new
        foreach ($contexts as $seed_key) {
            /** @var \LCI\Blend\Blendable\Context $blendContext */
            $blendContext = new Context($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendContext->setSeedsDir($seeds_dir);
            }

            if ( $blendContext->revertBlend() ) {
                $this->out($blendContext->getFieldKey().' context has been reverted to '.$seeds_dir);

            } else {
                $this->out($blendContext->getFieldKey().' context was not reverted', true);
            }
        }
    }


    /**
     * @param string $name
     * @return \LCI\Blend\Blendable\MediaSource
     */
    public function getBlendableMediaSource($name)
    {
        /** @var \LCI\Blend\Blendable\MediaSource $mediaSource */
        $mediaSource =  new MediaSource($this->modx, $this, $name);
        return $mediaSource
            ->setFieldName($name)
            ->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @param array $media_sources
     * @param string $seeds_dir
     */
    public function blendManyMediaSources($media_sources=[], $seeds_dir='')
    {
        // will update if element does exist or create new
        foreach ($media_sources as $seed_key) {
            /** @var \LCI\Blend\Blendable\MediaSource $blendMediaSource */
            $blendMediaSource = new MediaSource($this->modx, $this);
            if (!empty($seeds_dir)) {
                $blendMediaSource->setSeedsDir($seeds_dir);
            }
            if ($blendMediaSource->blendFromSeed($seed_key)) {
                $this->out($seed_key.' has been blended into ID: ');

            } elseif($blendMediaSource->isExists()) {
                // @TODO add Compare as option
                $this->out($seed_key.' media source already exists', true);
                if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                    if ($blendMediaSource->blendFromSeed($seed_key, true)) {
                        $this->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $media_sources
     * @param string $seeds_dir
     */
    public function revertBlendManyMediaSources($media_sources=[], $seeds_dir='')
    {
        // will update if system setting does exist or create new
        foreach ($media_sources as $seed_key) {
            /** @var \LCI\Blend\Blendable\MediaSource $blendMediaSource */
            $blendMediaSource = new MediaSource($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendMediaSource->setSeedsDir($seeds_dir);
            }

            if ( $blendMediaSource->revertBlend() ) {
                $this->out($blendMediaSource->getFieldName().' media source has been reverted to '.$seeds_dir);

            } else {
                $this->out($blendMediaSource->getFieldName().' media source was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Plugin with PHP
     * @param string $name
     * @return \LCI\Blend\Blendable\Plugin
     */
    public function getBlendablePlugin($name)
    {
        /** @var \LCI\Blend\Blendable\Plugin $plugin */
        $plugin =  new Plugin($this->modx, $this, $name);
        return $plugin->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @param array $plugins
     * @param string $seeds_dir
     */
    public function blendManyPlugins($plugins=[], $seeds_dir='')
    {
        // will update if element does exist or create new
        foreach ($plugins as $seed_key) {
            /** @var \LCI\Blend\Blendable\Plugin $blendPlugin */
            $blendPlugin = new Plugin($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendPlugin->setSeedsDir($seeds_dir);
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
     * @param string $seeds_dir
     */
    public function revertBlendManyPlugins($plugins=[], $seeds_dir='')
    {
        // will update if system setting does exist or create new
        foreach ($plugins as $seed_key) {
            /** @var \LCI\Blend\Blendable\Plugin $blendPlugin */
            $blendPlugin = new Plugin($this->modx, $this);
            if (!empty($seeds_dir)) {
                $blendPlugin->setSeedsDir($seeds_dir);
            }

            if ( $blendPlugin->revertBlend() ) {
                $this->out($blendPlugin->getFieldName().' plugin has been reverted to '.$seeds_dir);

            } else {
                $this->out($blendPlugin->getFieldName().' plugin was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Snippet with PHP
     * @param string $name
     * @return \LCI\Blend\Blendable\Snippet
     */
    public function getBlendableSnippet($name)
    {
        /** @var Snippet $snippet */
        $snippet =  new Snippet($this->modx, $this, $name);
        return $snippet->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @param array $snippets
     * @param string $seeds_dir
     */
    public function blendManySnippets($snippets=[], $seeds_dir='')
    {
        // will update if element does exist or create new
        foreach ($snippets as $seed_key) {
            /** @var \LCI\Blend\Blendable\Snippet $blendSnippet */
            $blendSnippet = new Snippet($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendSnippet->setSeedsDir($seeds_dir);
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
     * @param string $seeds_dir
     */
    public function revertBlendManySnippets($snippets=[], $seeds_dir='')
    {
        // will update if system setting does exist or create new
        foreach ($snippets as $seed_key) {
            /** @var Snippet $blendSnippet */
            $blendSnippet = new Snippet($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendSnippet->setSeedsDir($seeds_dir);
            }

            if ( $blendSnippet->revertBlend() ) {
                $this->out($blendSnippet->getFieldName().' snippet has been reverted to '.$seeds_dir);

            } else {
                $this->out($blendSnippet->getFieldName().' snippet was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to manually build a template
     * @param string $name
     * @return \LCI\Blend\Blendable\Template
     */
    public function getBlendableTemplate($name)
    {
        /** @var \LCI\Blend\Blendable\Template $template */
        $template =  new Template($this->modx, $this, $name);
        return $template->setSeedsDir($this->seeds_dir);
    }

    /**
     * @param array $templates
     * @param string $seeds_dir
     * @param bool $overwrite
     */
    public function blendManyTemplates($templates=[], $seeds_dir='', $overwrite=false)
    {
        // will update if template does exist or create new
        foreach ($templates as $seed_key) {

            /** @var \LCI\Blend\Blendable\Template $blendTemplate */
            $blendTemplate = new Template($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendTemplate->setSeedsDir($seeds_dir);
            }
            if ($blendTemplate->blendFromSeed($seed_key, $overwrite)) {
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
     * @param string $seeds_dir
     */
    public function revertBlendManyTemplates($templates=[], $seeds_dir='')
    {
        // will update if system setting does exist or create new
        foreach ($templates as $seed_key) {
            /** @var \LCI\Blend\Blendable\Template $blendTemplate */
            $blendTemplate = new Template($this->modx, $this, $this->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendTemplate->setSeedsDir($seeds_dir);
            }

            if ( $blendTemplate->revertBlend() ) {
                $this->out($blendTemplate->getFieldName().' template has been reverted to '.$seeds_dir);

            } else {
                $this->out($blendTemplate->getFieldName().' template was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to manually build a template variable
     * @param string $name
     * @return TemplateVariable
     */
    public function getBlendableTemplateVariable($name)
    {
        /** @var \LCI\Blend\Blendable\TemplateVariable $tv */
        $tv =  new TemplateVariable($this->modx, $this, $name);
        return $tv->setSeedsDir($this->seeds_dir);
    }

    /**
     * @param string $alias
     * @param  string $context
     * @return \LCI\Blend\Blendable\Resource
     */
    public function getBlendableResource($alias, $context='web')
    {
        /** @var \LCI\Blend\Blendable\Resource $resource */
        $resource =  new Resource($this->modx, $this, $alias, $context);
        return $resource
            ->setSeedsDir($this->getSeedsDir());
    }
    /**
     * @param array $resources
     * @param string $seeds_dir
     * @param bool $overwrite
     *
     * @return bool
     */
    public function blendManyResources($resources=[], $seeds_dir='', $overwrite=false)
    {
        $saved = true;
        // will update if resource does exist or create new
        foreach ($resources as $context => $seeds) {
            foreach ($seeds as $seed_key) {
                /** @var \LCI\Blend\Blendable\Resource $blendResource */
                $blendResource = new Resource($this->modx, $this, $this->getAliasFromSeedKey($seed_key), $context);

                if (!empty($seeds_dir)) {
                    $blendResource->setSeedsDir($seeds_dir);
                }

                if ($blendResource->blendFromSeed($seed_key, $overwrite)) {
                    $this->out($seed_key . ' has been blended into ID: ');

                } elseif ($blendResource->isExists()) {
                    // @TODO prompt Do you want to blend Y/N/Compare
                    $this->out($seed_key . ' already exists', true);
                    if ($this->prompt('Would you like to update?', 'Y') === 'Y') {
                        if ($blendResource->blendFromSeed($seed_key, true)) {
                            $this->out($seed_key . ' has been blended into ID: ');
                        }
                    }
                } else {
                    $this->out('There was an error saving ' . $seed_key, true);
                    echo 'There was an error saving ' . $seed_key; exit();
                    $saved = false;
                }
            }
        }

        return $saved;
    }

    /**
     * @param array $resources
     * @param string $seeds_dir
     * @param bool $overwrite
     *
     * @return bool
     */
    public function revertBlendManyResources($resources=[], $seeds_dir='', $overwrite=false)
    {
        $saved = true;
        // will update if resource does exist or create new
        foreach ($resources as $context => $seeds) {
            foreach ($seeds as $seed_key) {
                /** @var \LCI\Blend\Blendable\Resource $blendResource */
                $blendResource = new Resource($this->modx, $this, $this->getAliasFromSeedKey($seed_key), $context);

                if (!empty($seeds_dir)) {
                    $blendResource->setSeedsDir($seeds_dir);
                }
                if ($blendResource->revertBlend()) {
                    $this->out($seed_key . ' has been reverted ');

                } else {
                    $this->out('There was an error reverting resource ' . $seed_key, true);
                    $saved = false;
                }
            }
        }

        return $saved;
    }

    /**
     * @param string $key
     * @return \LCI\Blend\Blendable\SystemSetting
     */
    public function getBlendableSystemSetting($key='')
    {
        /** @var \LCI\Blend\Blendable\SystemSetting $systemSetting */
        $systemSetting =  new SystemSetting($this->modx, $this, $key);
        return $systemSetting->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $seeds_dir
     *
     * @return bool
     */
    public function blendManySystemSettings($settings=[], $seeds_dir='')
    {
        $success = true;
        // will update if system setting does exist or create new
        foreach ($settings as $data) {
            if (isset($data['columns'])) {
                $setting = $data['columns'];
            } else {
                $setting = $data;
                $data['columns'] = $data;
            }

            if (isset($setting['key'])) {
                $key = $setting['key'];

            } elseif (isset($setting['name'])) {
                $key = $setting['name'];

            } else {
                // Error: no name/key
                $success = false;
                continue;
            }

            $systemSetting = $this->getBlendableSystemSetting($key);
            if (!empty($seeds_dir)) {
                $systemSetting->setSeedsDir($seeds_dir);
            }

            if ($systemSetting->blendFromArray($data, true)) {
                $this->out($systemSetting->getFieldName().' setting has been blended');
            } else {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $seeds_dir
     *
     * @return bool
     */
    public function revertBlendManySystemSettings($settings=[], $seeds_dir='')
    {
        $success = true;
        // will update if system setting does exist or create new
        foreach ($settings as $data) {
            if (isset($data['columns'])) {
                $setting = $data['columns'];
            } else {
                $setting = $data;
                $data['columns'] = $data;
            }

            if (isset($setting['key'])) {
                $key = $setting['key'];

            } elseif (isset($setting['name'])) {
                $key = $setting['name'];

            } else {
                // Error: no name/key
                $success = false;
                continue;
            }

            $systemSetting = $this->getBlendableSystemSetting($key);

            if (!empty($seeds_dir)) {
                $systemSetting->setSeedsDir($seeds_dir);
            }

            if ( $systemSetting->revertBlend() ) {
                $this->out($systemSetting->getFieldName().' setting has been reverted to '.$seeds_dir);

            } else {
                $this->out($systemSetting->getFieldName().' setting was not reverted', true);
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
        return $this->userInteractionHandler->promptInput($question, $default);
    }

    /**
     * @param string $question
     * @param bool $default
     * @return bool
     */
    protected function promptConfirm($question, $default=true)
    {
        return $this->userInteractionHandler->promptConfirm($question, $default);
    }

    /**
     * @param string $question
     * @param string|mixed $default
     * @param array $options ~ ex: ['Option1' => 'value', 'Option2' => 'value2', ...]
     * @return mixed ~ selected value
     */
    protected function promptSelectOneOption($question, $default, $options=[])
    {
        return $this->userInteractionHandler->promptSelectOneOption($question, $default, $options);
    }

    /**
     * @param string $question
     * @param string|mixed $default
     * @param array $options ~ ex: ['Option1' => 'value', 'Option2' => 'value2', ...]
     * @return array ~ array of selected values
     */
    protected function promptSelectMultipleOptions($question, $default, $options=[])
    {
        return $this->userInteractionHandler->promptSelectMultipleOptions($question, $default, $options);
    }

    /**
     * @param string $message
     * @param bool $error
     */
    public function out($message, $error=false)
    {
        if ($error) {
            $this->userInteractionHandler->tellUser($message, userInteractionHandler::MASSAGE_ERROR);

        } else {
            $this->userInteractionHandler->tellUser($message, userInteractionHandler::MASSAGE_STRING);
        }
    }

    /**
     * @param string $message
     */
    public function outSuccess($message)
    {
        $this->userInteractionHandler->tellUser($message, userInteractionHandler::MASSAGE_SUCCESS);
    }

    /**
     * @param string $name
     * @param string $server_type
     * @param string|null $migration_path
     *
     * @return bool
     */
    public function createBlankMigrationClassFile($name, $server_type='master', $migration_path=null)
    {
        $migrationCreator = new MigrationsCreator($this->userInteractionHandler);

        if (empty($migration_path)) {
            $migration_path = $this->getMigrationPath();
        }

        echo PHP_EOL.__METHOD__.PHP_EOL;
        echo '----- P: '.$migration_path.PHP_EOL;

        $success = $migrationCreator
            ->setPathTimeStamp($this->getSeedsDir())
            ->setName($name)
            ->setDescription('')
            ->setServerType($server_type)
            ->setMigrationsPath($migration_path)
            ->createBlankMigrationClassFile();

        $this->logCreatedMigration($migrationCreator->getLogData());
        return $success;
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
            /** @var \LCI\Blend\Blendable\Chunk $blendChunk */
            $blendChunk = new Chunk($this->modx, $this, $chunk->get('name'));
            $seed_key = $blendChunk
                ->setSeedsDir($this->getMigrationName('chunk', $name))
                ->seed();
            $this->out("Chunk: ".$chunk->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $keys);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createChunkMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
    public function makeContextSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modContext', $criteria);

        foreach ($collection as $context) {
            /** @var Context $blendContext */
            $blendContext = new Context($this->modx, $this, $context->get('key'));
            $seed_key = $blendContext
                ->setSeedsDir($this->getMigrationName('context', $name))
                ->seed();
            $this->out("Context: ".$context->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $keys);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createContextMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
    public function makeMediaSourceSeeds($criteria, $server_type='master', $name=null, $create_migration_file=true)
    {
        $keys = [];
        $collection = $this->modx->getCollection('modMediaSource', $criteria);

        foreach ($collection as $mediaSource) {
            /** @var MediaSource $blendMediaSource */
            $blendMediaSource = new MediaSource($this->modx, $this, $mediaSource->get('name'));
            $seed_key = $blendMediaSource
                ->setSeedsDir($this->getMigrationName('mediaSource', $name))
                ->seed();
            $this->out("Media Source: ".$mediaSource->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $keys);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createMediaSourceMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
            /** @var \LCI\Blend\Blendable\Plugin $blendPlugin */
            $blendPlugin = new Plugin($this->modx, $this, $plugin->get('name'));
            $seed_key = $blendPlugin
                ->setSeedsDir($this->getMigrationName('plugin', $name))
                ->seed();
            $this->out("Plugin: ".$plugin->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $keys);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createPluginMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
        $keys = [
            'web' => []
        ];

        $collection = $this->modx->getCollection('modResource', $criteria);
        foreach ($collection as $resource) {
            $blendResource = new Resource($this->modx, $this, $resource->get('alias'), $resource->get('context_key'));
            $seed_key = $blendResource
                ->setSeedsDir($this->getMigrationName('resource', $name))
                ->seed($resource);
            $this->out("ID: ".$resource->get('id').' Key: '.$seed_key);

            if (!isset($keys[$resource->get('context_key')])) {
                $keys[$resource->get('context_key')] = [];
            }

            $keys[$resource->get('context_key')][] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $keys);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createResourceMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
            /** @var \LCI\Blend\Blendable\Snippet $blendSnippet */
            $blendSnippet = new Snippet($this->modx, $this, $snippet->get('name'));
            $seed_key = $blendSnippet
                ->setSeedsDir($this->getMigrationName('snippet', $name))
                ->seed();
            $this->out("Snippet: ".$snippet->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $keys);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createSnippetMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
            /** @var \LCI\Blend\Blendable\SystemSetting $blendableSetting */
            $blendableSetting = $this->getBlendableSystemSetting($setting->get('key'));
            $setting_data[] = $blendableSetting->seedToArray();
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
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $setting_data);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createSystemSettingsMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
            //exit();
            /** @var \LCI\Blend\Blendable\Template $blendTemplate */
            $blendTemplate = new Template($this->modx, $this, $template->get('templatename'));
            $seed_key = $blendTemplate
                ->setSeedsDir($this->getMigrationName('template', $name))
                ->seed();
            $this->out("Template ID: ".$template->get('id').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $keys);

            $migrationCreator
                ->setPathTimeStamp($this->getSeedsDir())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->getMigrationPath())
                ->createTemplateMigrationClassFile();

            $this->logCreatedMigration($migrationCreator->getLogData());
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
            'mediaSources' => $this->makeMediaSourceSeeds(null, $server_type, $name, false),
            'contexts' => $this->makeContextSeeds(null, $server_type, $name, false),
            'chunks' => $this->makeChunkSeeds(null, $server_type, $name, false),
            'plugins' => $this->makePluginSeeds(null, $server_type, $name, false),
            'resources' => $this->makeResourceSeeds(null, $server_type, $name, false),
            'snippets' => $this->makeSnippetSeeds(null, $server_type, $name, false),
            'systemSettings' => $this->makeSystemSettingSeeds(null, $server_type, $name, false),
            'templates' => $this->makeTemplateSeeds(null, $server_type, $name, false)
        ];

        /** @var MigrationsCreator $migrationCreator */
        $migrationCreator = new MigrationsCreator($this->userInteractionHandler, $site_data);

        $migrationCreator
            ->setPathTimeStamp($this->getSeedsDir())
            ->setName($name)
            ->setDescription('')
            ->setServerType($server_type)
            ->setMigrationsPath($this->getMigrationPath())
            ->createSiteMigrationClassFile();

        $this->logCreatedMigration($migrationCreator->getLogData());
    }

    /**
     * @param string $method
     * @param bool $prompt
     */
    public function install($method='up', $prompt=false)
    {
        $migration_name = 'install_blender';
        $custom_migration_dir = __DIR__.'/Migrations/Blend/';

        $this->runInstallMigration($migration_name, $custom_migration_dir, null, $method, $prompt);
    }

    /**
     * @param string $migration_name
     * @param string|null $custom_migration_path
     * @param string|null $seed_root_path
     * @param string $method
     * @param bool $prompt
     */
    protected function runInstallMigration($migration_name, $custom_migration_path=null, $seed_root_path=null, $method='up', $prompt=false)
    {
        // new blender for each instance
        $config = $this->config;

        if (!empty($custom_migration_path)) {
            $config['migrations_path'] = $custom_migration_path;
        }
        if (!empty($seed_root_path)) {
            $config['seeds_path'] = $seed_root_path;
        }

        $blender = new Blender($this->modx, $this->getUserInteractionHandler(), $config);

        /** @var Migrations $migrationProcessClass */
        $migrationProcessClass = $blender->loadMigrationClass($migration_name, $blender);

        if (!$migrationProcessClass instanceof Migrations) {
            $this->out('File is not an instance of LCI\Blend\Migrations: '.$migration_name, true);
            $this->out('Did not process, verify it is in the proper directory', true);

        } elseif ($method == 'up' && !$this->isBlendInstalledInModx()) {

            $migrationProcessClass->up();

            /** @var \BlendMigrations $migration */
            $migration = $this->modx->newObject($this->blend_class_object);
            if ($migration) {
                $migration->set('name', $migration_name);
                $migration->set('type', 'master');
                $migration->set('description', $migrationProcessClass->getDescription());
                $migration->set('version', $migrationProcessClass->getVersion());
                $migration->set('status', 'up_complete');
                $migration->set('created_at', date('Y-m-d H:i:s'));
                $migration->set('processed_at', date('Y-m-d H:i:s'));
                if ($migration->save() ) {
                    $this->outSuccess($migration_name.' ran and logged');
                } else {
                    $this->out($migration_name . ' did not log correctly', true);
                }

                // does the migration directory exist?
                if (!file_exists($this->getMigrationPath())) {
                    $create = true;
                    if ($prompt) {
                        $response = $this->prompt('Create the following directory for migration files? (y/n) '.PHP_EOL
                            .$this->getMigrationPath(), 'y');
                        if (strtolower(trim($response)) != 'y') {
                            $create = false;
                        }
                    }
                    if ($create) {
                        mkdir($this->getMigrationPath(), 0700, true);
                        $this->outSuccess('Created migration directory: '. $this->getMigrationPath());
                    }
                }

            } else {
                $this->out($migration_name . ' did not log correctly', true);
            }

        } elseif ($method == 'down') {
            $migrationProcessClass->down();

            /** @var \BlendMigrations $migration */
            $migration = $this->modx->getObject($this->blend_class_object, ['name' => $migration_name]);
            if ($migration) {
                $migration->set('name', $migration_name);
                $migration->set('description', $migrationProcessClass->getDescription());
                $migration->set('version', $migrationProcessClass->getVersion());
                $migration->set('status', 'down_complete');
                $migration->set('processed_at', date('Y-m-d H:i:s'));
                $migration->save();
            }

        }
    }

    /**
     * @param string $version_key
     */
    protected function cacheUserInstallConfig($version_key, $config=[])
    {
        $simpleCache = new SimpleCache(BLEND_CACHE_DIR . 'modx/');
        $simpleCache->set('install-config-'.$version_key, $config);
    }

    /**
     * @param string $method
     */
    public function update($method='up')
    {
        $current_vesion = $this->modx->getOption('blend.version');

        // new blender for each instance
        $config = $this->config;
        $config['migrations_path'] = __DIR__.'/Migrations/Blend/';

        $blender = new Blender($this->modx, $this->getUserInteractionHandler(), $config);

        foreach ($this->update_migrations as $v => $migration_name) {
            if (version_compare($v, $current_vesion) === 1 ) {
                // can not use as xPDO get queries fill the SELECT with the DB fields and since we are adding one this is a SQL error
                //$blender->runMigration($method, 'master', 0, 0, $migration_name);

                /** @var Migrations $migrationProcessClass */
                $migrationProcessClass = $this->loadMigrationClass($migration_name, $blender);

                if (!$migrationProcessClass instanceof Migrations) {
                    $this->out('File is not an instance of LCI\Blend\Migrations: '.$migration_name, true);
                    $this->out('Did not process, verify it is in the proper directory', true);

                } elseif ($method == 'up' && $this->isBlendInstalledInModx()) {

                    $migrationProcessClass->up();

                    /** @var \BlendMigrations $migration */
                    $migration = $this->modx->newObject($this->blend_class_object);
                    if ($migration) {
                        $migration->set('name', $migration_name);
                        $migration->set('type', 'master');
                        $migration->set('description', $migrationProcessClass->getDescription());
                        $migration->set('version', $migrationProcessClass->getVersion());
                        $migration->set('status', 'up_complete');
                        $migration->set('created_at', date('Y-m-d H:i:s'));
                        $migration->set('processed_at', date('Y-m-d H:i:s'));
                        if ($migration->save() ) {
                            $this->outSuccess('Blend updated to '.$v);
                        } else {
                            $this->out('Blend did not update to '.$v, true);
                        }

                    } else {
                        $this->out('Blender could not save the update to '.$v, true);
                    }

                } elseif ($method == 'down') {
                    $migrationProcessClass->down();

                    /** @var \BlendMigrations $migration */
                    $migration = $this->modx->getObject($this->blend_class_object, ['name' => $migration_name]);
                    if ($migration) {
                        $migration->set('name', $migration_name);
                        $migration->set('description', $migrationProcessClass->getDescription());
                        $migration->set('version', $migrationProcessClass->getVersion());
                        $migration->set('status', 'down_complete');
                        $migration->set('processed_at', date('Y-m-d H:i:s'));
                        $migration->save();
                    }

                }

            }
        }

    }

    /**
     * @return bool
     */
    public function requireUpdate()
    {
        $upgrade = false;

        $current_vesion = $this->modx->getOption('blend.version');
        //                                      FILE version,        DB Version
        if ( $this->isBlendInstalledInModx() && ( !$current_vesion || version_compare($this->getVersion(), $current_vesion)) ) {
            $upgrade = true;
        }

        return $upgrade;
    }

    /**
     * @return bool
     */
    public function isBlendInstalledInModx()
    {
        try {
            $table = $this->modx->getTableName($this->blend_class_object);
            if ($this->modx->query("SELECT 1 FROM {$table} LIMIT 1") === false) {
                return false;
            }
        } catch (Exception $exception) {
            // We got an exception == table not found
            return false;
        }

        /** @var \xPDOQuery $query */
        $query = $this->modx->newQuery($this->blend_class_object);
        $query->select('id');
        $query->where([
            'name' => 'install_blender',
            'status' => 'up_complete'
        ]);
        $query->sortBy('name');

        $installMigration = $this->modx->getObject($this->blend_class_object, $query);
        if ($installMigration instanceof \BlendMigrations || $installMigration instanceof \LCI\Blend\Model\xPDO\BlendMigrations) {
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
            $blender = new Blender($this->modx, $this->getUserInteractionHandler(), $this->config);

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
        $migrationCollection = $this->modx->getCollection($this->blend_class_object);

        $blendMigrations = [];

        /** @var \BlendMigrations $migration */
        foreach ($migrationCollection as $migration) {
            $blendMigrations[$migration->get('name')] = $migration;
        }

        $migration_dir = $this->getMigrationPath();
        $this->out('Searching '.$migration_dir);

        $reload = false;
        /** @var \DirectoryIterator $file */
        foreach (new \DirectoryIterator($this->getMigrationPath()) as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {

                $name = $file->getBasename('.php');
                // @TODO query DB! and test this method
                if (!isset($blendMigrations[$name])) {
                    $this->out('Create new '.$name);
                    /** @var Migrations $migrationProcessClass */
                    $migrationProcessClass = $this->loadMigrationClass($name, $this);

                    /** @var \BlendMigrations $migration */
                    $migration = $this->modx->newObject($this->blend_class_object);
                    $migration->set('name', $name);
                    $migration->set('status', 'ready');
                    if ($migrationProcessClass instanceof Migrations) {
                        $migration->set('description', $migrationProcessClass->getDescription());
                        $migration->set('version', $migrationProcessClass->getVersion());
                        $migration->set('author', $migrationProcessClass->getAuthor());
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

        $file = $blender->getMigrationPath().$name.'.php';
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
     * @param $type
     * @param null $name
     * @return string
     */
    public function getMigrationName($type, $name=null)
    {
        $format = new Format($this->seeds_dir);
        return $format->getMigrationName($type, $name);
    }

    /**
     * @param string $name
     * @param string $type ~ chunk, plugin, resource, snippet, systemSettings, template, site
     *
     * @return bool
     */
    public function removeMigrationFile($name, $type)
    {
        // @TODO refactor for setting $name
        $class_name = $this->getMigrationName($type, $name);

        $removed = false;
        $migration_file = $this->getMigrationPath() . $class_name . '.php';
        if (file_exists($migration_file)) {
            if (unlink($migration_file)) {
                $removed = true;
                $migration = $this->modx->getObject($this->blend_class_object, ['name' => $class_name]);
                if (is_object($migration) && $migration->remove()) {
                    $this->out($class_name . ' migration has been removed from the blend_migrations table');

                }
            } else {
                $this->out($class_name . ' migration has not been removed from the blend_migrations table', true);
            }

        } else {
            $this->out($this->getMigrationPath() . $class_name . '.php migration could not be found to remove', true);
        }

        return $removed;
    }
    /**
     * @param int $id
     *
     * @return bool|array
     */
    public function getResourceSeedKeyFromID($id)
    {
        if (!isset($this->resource_id_map[$id])) {
            $seed_key = $context = false;
            $resource = $this->modx->getObject('modResource', $id);
            if ($resource) {
                $context = $resource->get('context_key');
                if (!isset($this->resource_seek_key_map[$context])) {
                    $this->resource_seek_key_map[$context] = [];
                }
                $seed_key = $this->getSeedKeyFromAlias($resource->get('alias'));
                $this->resource_seek_key_map[$context][$seed_key] = $id;
            }
            $this->resource_id_map[$id] = [
                'context' => $context,
                'seed_key' => $seed_key
            ];
        }

        return $this->resource_id_map[$id];
    }

    /**
     * @param string $seed_key
     * @param string $context
     *
     * @return bool|int
     */
    public function getResourceIDFromSeedKey($seed_key, $context='web')
    {
        if (!isset($this->resource_seek_key_map[$context])) {
            $this->resource_seek_key_map[$context] = [];
        }
        if (!isset($this->resource_seek_key_map[$context][$seed_key])) {
            $id = false;
            $alias = $this->getAliasFromSeedKey($seed_key);
            $resource = $this->modx->getObject('modResource', ['alias' => $alias, 'context_key' => $context]);
            if ($resource) {
                $id = $resource->get('id');
                $this->resource_seek_key_map[$context][$seed_key] = $id;
                $this->resource_id_map[$id] = [
                    'context' => $context,
                    'seed_key' => $seed_key
                ];
            }
            $this->resource_seek_key_map[$context][$seed_key] = $id;
        }

        return $this->resource_seek_key_map[$context][$seed_key];
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
     * @deprecated
     * @param string $name
     * @param string $type ~ template, template-variable, chunk, snippet or plugin
     * @return string
     */
    public function getElementSeedKeyFromName($name, $type)
    {
        return $type.'_'.$this->getSeedKeyFromName($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getSeedKeyFromName($name)
    {
        // @TODO review
        return str_replace('/', '#', $name);
    }

    /**
     * @param string $seed_key
     * @return string
     */
    public function getNameFromSeedKey($seed_key)
    {
        return str_replace('#', '/', $seed_key);
    }

    /**
     * @param $data
     */
    protected function logCreatedMigration($data)
    {
        try {
            /** @var BlendMigrations $migration */
            $migration = $this->modx->newObject($this->blend_class_object);
            if ($migration) {
                $migration->fromArray($data);
                $migration->save();
            }
        } catch (Exception $exception) {
            $this->out($exception->getMessage(), true);
        }
    }
}
/**
 * id | element_class | name | data | action ??
 */
