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
use LCI\Blend\Helpers\BlendableLoader;
use LCI\MODX\Console\Helpers\UserInteractionHandler;
use Exception;

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

    /** @var BlendableLoader */
    protected $blendableLoader;

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
    public function __construct(modX $modx, UserInteractionHandler $userInteractionHandler, $config = [])
    {
        $this->modx = $modx;

        $this->modx_version_info = $this->modx->getVersionData();

        $this->userInteractionHandler = $userInteractionHandler;

        if (version_compare($this->modx_version_info['full_version'], '3.0') >= 0) {
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
            'migration_templates_path' => __DIR__.'/Migrations/templates/',
            'migrations_path' => $blend_modx_migration_dir.'database/migrations/',
            'seeds_path' => $blend_modx_migration_dir.'database/seeds/',
            'model_dir' => __DIR__.($this->xpdo_version >= 3 ? '/' : '/xpdo2/'),
            'extras' => [
                'tagger' => false
            ]
        ];
        $this->config = array_merge($this->config, $config);

        $this->seeds_dir = date('Y_m_d_His');

        $tagger_path = $this->modx->getOption('tagger.core_path', null, $this->modx->getOption('core_path').'components/tagger/').'model/tagger/';
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

        $this->blendableLoader = new BlendableLoader($this, $this->modx, $this->userInteractionHandler);
    }

    /**
     * @deprecated
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        // How to mark as deprecated?
        if (method_exists($this->blendableLoader, $name)) {

            $message = get_class($this) . '->'.$name.'() has been deprecated, please use ' . get_class($this) . '->getBlendableLoader()->' . $name;
            //trigger_error($message, E_USER_WARNING);
            $this->modx->log(\modX::LOG_LEVEL_ERROR, $message);

            return call_user_func_array(array($this->blendableLoader, $name), $arguments);

        } else {
            throw new Exception('Call to undefined Method ' . get_class($this) . '->' . $name);
        }
    }

    /**
     * @return BlendableLoader
     */
    public function getBlendableLoader(): BlendableLoader
    {
        return $this->blendableLoader;
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
    public function getSeedsPath($directory_key = null)
    {
        $seed_path = $this->config['seeds_path'];
        if (!empty($directory_key)) {
            $seed_path .= trim($directory_key, '/').DIRECTORY_SEPARATOR;
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
    public function getBlendMigrationCollection($reload = false, $dir = 'ASC', $count = 0, $id = 0, $name = null)
    {
        if (!$this->blendMigrations || $reload) {
            $blendMigrations = [];

            /** @var \xPDOQuery $query */
            $query = $this->modx->newQuery($this->blend_class_object);
            if ($id > 0) {
                $query->where(['id' => $id]);
            } elseif (!empty($name)) {
                $query->where(['name' => $name]);
            }
            // @TODO need a ran sequence column to better order of down
            $query->sortBy('name', $dir);
            if ($count > 0) {
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

    /**
     * @param bool $refresh
     * @return array
     */
    public function getCategoryMap($refresh = false)
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
     * @param string $message
     * @param bool $error
     */
    public function out($message, $error = false)
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
    public function createBlankMigrationClassFile($name, $server_type = 'master', $migration_path = null)
    {
        $migrationCreator = new MigrationsCreator($this->userInteractionHandler);

        if (empty($migration_path)) {
            $migration_path = $this->getMigrationPath();
        }

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
     * @return SeedMaker
     */
    public function getSeedMaker()
    {
        return new SeedMaker($this->modx, $this);
    }

    /**
     * @param string $method
     * @param bool $prompt
     */
    public function install($method = 'up', $prompt = false)
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
    protected function runInstallMigration($migration_name, $custom_migration_path = null, $seed_root_path = null, $method = 'up', $prompt = false)
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
                if ($migration->save()) {
                    $this->outSuccess($migration_name.' ran and logged');
                } else {
                    $this->out($migration_name.' did not log correctly', true);
                }

                // does the migration directory exist?
                if (!file_exists($this->getMigrationPath())) {
                    $create = true;
                    if ($prompt) {
                        $create = $this->userInteractionHandler->promptConfirm('Create the following directory for migration files?' . PHP_EOL
                            .$this->getMigrationPath(), true);
                    }
                    if ($create) {
                        mkdir($this->getMigrationPath(), 0700, true);
                        $this->outSuccess('Created migration directory: '.$this->getMigrationPath());
                    }
                }

            } else {
                $this->out($migration_name.' did not log correctly', true);
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
     * @param string $method
     */
    public function update($method = 'up')
    {
        $current_vesion = $this->modx->getOption('blend.version');

        // new blender for each instance
        $config = $this->config;
        $config['migrations_path'] = __DIR__.'/Migrations/Blend/';

        $blender = new Blender($this->modx, $this->getUserInteractionHandler(), $config);

        foreach ($this->update_migrations as $v => $migration_name) {
            if (version_compare($v, $current_vesion) === 1) {
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
                        if ($migration->save()) {
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
        if ($this->isBlendInstalledInModx() && (!$current_vesion || version_compare($this->getVersion(), $current_vesion))) {
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
    public function runMigration($method = 'up', $type = 'master', $count = 0, $id = 0, $name = null)
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

            if (($server_type != $type) || ($method == 'up' && $status == 'up_complete') || ($method == 'down' && $status != 'up_complete')) {
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

            if (class_exists($name)) {
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
    public function getMigrationName($type, $name = null)
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
        $migration_file = $this->getMigrationPath().$class_name.'.php';
        if (file_exists($migration_file)) {
            if (unlink($migration_file)) {
                $removed = true;
                $migration = $this->modx->getObject($this->blend_class_object, ['name' => $class_name]);
                if (is_object($migration) && $migration->remove()) {
                    $this->out($class_name.' migration has been removed from the blend_migrations table');

                }
            } else {
                $this->out($class_name.' migration has not been removed from the blend_migrations table', true);
            }

        } else {
            $this->out($this->getMigrationPath().$class_name.'.php migration could not be found to remove', true);
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
    public function getResourceIDFromSeedKey($seed_key, $context = 'web')
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
