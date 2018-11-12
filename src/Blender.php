<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/29/2017
 * Time: 3:33 PM
 */

namespace LCI\Blend;

use LCI\Blend\Console\Blend;
use LCI\Blend\Exception\MigratorException;
use LCI\Blend\Helpers\Format;
use LCI\Blend\Helpers\BlendableLoader;
use LCI\Blend\Migrations\MigrationsCreator;
use LCI\Blend\Migrations\Migrator;
use LCI\MODX\Console\Helpers\UserInteractionHandler;
use modX;
use Symfony\Component\Console\Output\OutputInterface;

class Blender
{
    /** @var string ~ version number of the project */
    private $version = '1.1.4';

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

    /** @var string  */
    protected $project = 'local';

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

    const VERBOSITY_QUIET = OutputInterface::VERBOSITY_QUIET;
    const VERBOSITY_NORMAL = OutputInterface::VERBOSITY_NORMAL;
    const VERBOSITY_VERBOSE = OutputInterface::VERBOSITY_VERBOSE;
    const VERBOSITY_VERY_VERBOSE = OutputInterface::VERBOSITY_VERY_VERBOSE;
    const VERBOSITY_DEBUG = OutputInterface::VERBOSITY_DEBUG;

    /**
     * Blender constructor.
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
            'history_path' => $blend_modx_migration_dir.'database/history/',
            'model_dir' => __DIR__.($this->xpdo_version >= 3 ? '/' : '/xpdo2/'),
            'extras' => [
                'tagger' => false
            ]
        ];

        $this->setVerbose();

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
     * @throws MigratorException
     */
    public function __call($name, $arguments)
    {
        // How to mark as deprecated?
        if (method_exists($this->blendableLoader, $name)) {

            $message = get_class($this) . '->'.$name.'() has been deprecated, please use ' . get_class($this) . '->getBlendableLoader()->' . $name;
            trigger_error($message, E_USER_WARNING);
            $this->modx->log(\modX::LOG_LEVEL_ERROR, $message);

            return call_user_func_array(array($this->blendableLoader, $name), $arguments);

        } else {
            throw new MigratorException('Call to undefined Method ' . get_class($this) . '->' . $name);
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
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getVerbose(): int
    {
        return $this->config['verbose'];
    }

    /**
     * @param int $verbose
     * @see https://symfony.com/doc/current/console/verbosity.html
     * @return $this
     */
    public function setVerbose(int $verbose=self::VERBOSITY_NORMAL)
    {
        $this->config['verbose'] = $verbose;
        return $this;
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
     * @param string|null $directory_key
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
     * @param string|null $directory_key
     * @return string
     */
    public function getHistoryPath($directory_key = null)
    {
        $seed_path = $this->config['history_path'];
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
     * @param int $verbose
     * @param bool $error
     */
    public function out($message, $verbose=Blender::VERBOSITY_NORMAL, $error = false)
    {
        if ($this->getVerbose() >= $verbose) {
            if ($error) {
                $this->userInteractionHandler->tellUser($message, userInteractionHandler::MASSAGE_ERROR);

            } else {
                $this->userInteractionHandler->tellUser($message, userInteractionHandler::MASSAGE_STRING);
            }
        }
    }

    /**
     * @param string $message
     * @param int $verbose
     */
    public function outError($message, $verbose=Blender::VERBOSITY_NORMAL)
    {
        if ($this->getVerbose() >= $verbose) {
            $this->userInteractionHandler->tellUser($message, userInteractionHandler::MASSAGE_ERROR);
        }
    }

    /**
     * @param string $message
     * @param int $verbose
     */
    public function outSuccess($message, $verbose=Blender::VERBOSITY_NORMAL)
    {
        if ($this->getVerbose() >= $verbose) {
            $this->userInteractionHandler->tellUser($message, userInteractionHandler::MASSAGE_SUCCESS);
        }
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
            ->setVerbose($this->getVerbose())
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
     * @throws MigratorException
     */
    /**
     * @param string $method
     * @param bool $prompt
     * @throws MigratorException
     */
    public function install($method = 'up', $prompt = false)
    {
        $config = $this->config;

        $config['migrations_path'] = __DIR__.'/database/migrations/';

        $blender = new Blender($this->modx, $this->getUserInteractionHandler(), $config);
        $blender->setProject('lci/blend');

        $migrator = new Migrator($blender, $this->modx, 'lci/blend');
        $migrator
            ->setDelayLogging(true)
            ->setCheckInstallLog($method == 'up' ? false : true)
            ->runMigration($method);

        // does the migration directory exist?
        if (!file_exists($this->getMigrationPath())) {
            $create = true;
            if ($this->getVerbose() >= Blender::VERBOSITY_NORMAL || $prompt) {
                $create = $this->getUserInteractionHandler()->promptConfirm('Create the following directory for migration files?'.PHP_EOL
                    .$this->getMigrationPath(), true);
            }

            if ($create) {
                mkdir($this->getMigrationPath(), 0700, true);
                $this->outSuccess('Created migration directory: '.$this->getMigrationPath());
            }
        }

        /** @var \LCI\Blend\Blendable\SystemSetting $systemSetting */
        $systemSetting = $this->getBlendableLoader()->getBlendableSystemSetting('blend.version');
        $systemSetting
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldValue($this->getVersion())
            ->setFieldArea('Blend')
            ->blend(true);

        $this->modx->cacheManager->refresh();
    }

    /**
     * @throws MigratorException
     */
    public function uninstall()
    {
        $this->install('down');
    }

    /**
     * @param string $method
     * @throws MigratorException
     */
    public function update($method = 'up')
    {
        $current_version = $this->modx->getOption('blend.version');
        $this->install($method);
    }

    /**
     * @return bool
     */
    public function requireUpdate()
    {
        $upgrade = false;

        $current_version = $this->modx->getOption('blend.version');
        //                                      FILE version,        DB Version
        if ($this->isBlendInstalledInModx() && (!$current_version || version_compare($this->getVersion(), $current_version) === 1)) {
            $this->outError('MODX System Setting Version: '. $current_version.' Code version: '.$this->getVersion(), Blender::VERBOSITY_DEBUG);
            $upgrade = true;
        }

        return $upgrade;
    }

    /**
     * @param bool $check_install_log
     * @return bool
     */
    public function isBlendInstalledInModx($check_install_log=true)
    {
        $installed = false;
        try {
            $table = $this->modx->getTableName($this->blend_class_object);
            if ($rs = $this->modx->query("SELECT 1 FROM {$table} LIMIT 0") === false) {
                $this->out(__METHOD__.' table not found ', Blender::VERBOSITY_DEBUG);
                return false;
            }
            $installed = true;

        } catch (\Exception $exception) {
            $this->out(__METHOD__ . ' Exception: '. $exception->getMessage(), Blender::VERBOSITY_DEBUG);
            // We got an exception == table not found
            return false;
        }

        if ($check_install_log) {
            $installed = false;
            /** @var \xPDOQuery $query */
            $query = $this->modx->newQuery($this->blend_class_object);
            $query->select('id');
            $query->where([
                'name:IN' => ['InstallBlender', 'install_blender'],
                'status' => 'up_complete'
            ]);
            $query->sortBy('name');

            $installMigration = $this->modx->getObject($this->blend_class_object, $query);
            if ($installMigration instanceof \BlendMigrations || $installMigration instanceof \LCI\Blend\Model\xPDO\BlendMigrations) {
                $this->out(print_r($installMigration->toArray(), true), Blender::VERBOSITY_DEBUG);
                $installed = true;
            }
            $this->out(__METHOD__ . ' Blend install log not found', Blender::VERBOSITY_DEBUG);
        }

        return $installed;
    }

    /**
     * @param string $method
     * @param string $type
     * @param int $count
     * @param int $id
     * @param null|string $name
     * @throws MigratorException
     */
    public function runMigration($method = 'up', $type = 'master', $count = 0, $id = 0, $name = null)
    {
        /** @var Migrator $migrator */
        $migrator = new Migrator($this, $this->modx, $this->project);
        $migrator->runMigration($method, $type, $count, $id, $name);
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
        $class_name = $this->getMigrationName($type, $name);

        $removed = false;
        $migration_file = $this->getMigrationPath().$class_name.'.php';
        if (file_exists($migration_file)) {
            if (unlink($migration_file)) {
                $removed = true;
                $migration = $this->modx->getObject($this->blend_class_object, ['name' => $class_name]);
                if (is_object($migration) && $migration->remove()) {
                    $this->out($class_name.' migration has been removed from the blend_migrations table', Blender::VERBOSITY_VERY_VERBOSE);

                }
            } else {
                $this->outError($class_name.' migration has not been removed from the blend_migrations table');
            }

        } else {
            $this->outError($this->getMigrationPath().$class_name.'.php migration could not be found to remove');
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
     * @param string $alias
     * @param string $context
     * @return bool|int
     */
    public function getResourceIDFromLocalAlias($alias, $context='web')
    {
        $seed_key = $this->getSeedKeyFromAlias($alias);
        return $this->getResourceIDFromSeedKey($seed_key, $context);
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
     * @param string $project
     * @return Blender
     */
    public function setProject(string $project): Blender
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @param array $data
     */
    protected function logCreatedMigration(array $data)
    {
        $migrator = new Migrator($this, $this->modx, 'local');
        $migrator->logMigration($data);
    }
}
