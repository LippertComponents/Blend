<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/2/18
 * Time: 2:35 PM
 */

namespace LCI\Blend\Migrations;

use LCI\Blend\Blender;
use LCI\Blend\Migrations;
use modX;
use Symfony\Component\Console\Output\OutputInterface;

class Migrator
{
    /** @var Blender */
    protected $blender;

    /** @var \modX */
    protected $modx;

    /** @var string  */
    protected $project = 'local';

    /** @var int @see https://symfony.com/doc/current/console/verbosity.html */
    protected $verbose = OutputInterface::VERBOSITY_NORMAL;

    /** @var array  */
    protected $blendMigrations = [];

    /** @var string ~ up|down */
    protected $migration_method = 'up';

    /** @var string  */
    protected $migration_type = 'master';

    /** @var int  */
    protected $migration_count = 0;

    /** @var int  */
    protected $migration_id = 0;

    /** @var string  */
    protected $migration_name = '';

    /**
     * Migrator constructor.
     * @param Blender $blender
     * @param modX $modx
     * @param string $project
     */
    public function __construct(Blender $blender, modX $modx, string $project)
    {
        $this->blender = $blender;
        $this->modx = $modx;
        $this->project = $project;
    }

    /**
     * @return int
     */
    public function getVerbose(): int
    {
        return $this->verbose;
    }

    /**
     * @param int $verbose
     * @see https://symfony.com/doc/current/console/verbosity.html
     * @return Migrator
     */
    public function setVerbose(int $verbose): Migrator
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * @param $data
     */
    public function logMigration($data)
    {
        if ($this->blender->isBlendInstalledInModx()) {
            try {
                /** @var \BlendMigrations $migration */
                $migration = $this->modx->newObject($this->blender->getBlendClassObject());
                if ($migration) {
                    $migration->fromArray($data);
                    $migration->save();
                }
            } catch (\Exception $exception) {
                $this->outError($exception->getMessage());
            }
        }
    }

    /**
     * @param string $method
     * @param string $type
     * @param int $count
     * @param int $id
     * @param null|string $name
     * @throws \Exception
     */
    public function runMigration($method = 'up', $type = 'master', $count = 0, $id = 0, $name = null)
    {
        $this->migration_method = $method;
        $this->migration_type = $type;
        $this->migration_count = $count;
        $this->migration_id = $id;
        $this->migration_name = $name;

        // 1. Get all migrations currently in DB:
        $this->getBlendMigrationCollection(false);

        // 2. Load migration files:
        if ($this->migration_method == 'up') {
            $loaded_migrations = $this->retrieveMigrationFiles();

            if (!$this->blender->isBlendInstalledInModx()) {
                $this->runLoadedFileMigrations($loaded_migrations);
                return;
            }
        }

        $this->runExistingDBMigrations();
    }

    /**
     * @return array ~ ['MigrationName' => MigrationObject, ...]
     */
    protected function retrieveMigrationFiles()
    {
        // 1. Get all migrations currently in DB:
        $blendMigrations = $this->getBlendMigrationCollection();

        $this->out('Searching directory for Migration classes ' . $this->blender->getMigrationPath());

        $loaded_migrations = [];
        $reload = false;
        /** @var \DirectoryIterator $file */
        foreach (new \DirectoryIterator($this->blender->getMigrationPath()) as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {

                $name = $file->getBasename('.php');
                //exit();
                if (!isset($blendMigrations[$name])) {
                    $new_migrations[] = $name;
                    /** @var Migrations $migrationProcessClass */
                    $migrationProcessClass = $this->loadMigrationClass($name, $this->blender);

                    $log_data = [
                        'project' => $this->project,
                        'name' => $name,
                        'status' => 'ready',
                    ];
                    if ($migrationProcessClass instanceof Migrations) {
                        $log_data['author'] = $migrationProcessClass->getAuthor();
                        $log_data['description'] = $migrationProcessClass->getDescription();
                        $log_data['type'] = $migrationProcessClass->getType();
                        $log_data['version'] = $migrationProcessClass->getVersion();

                        $loaded_migrations[$name] = $migrationProcessClass;
                    }

                    $this->logMigration($log_data);

                    $reload = true;
                }
            }
        }

        ksort($loaded_migrations);

        foreach ($loaded_migrations as $name => $migration) {
            $this->out('Found new Migration class '.$name);
        }

        if ($reload) {
            $this->getBlendMigrationCollection(true);
        }

        return $loaded_migrations;
    }

    /**
     * @throws \Exception
     */
    protected function runExistingDBMigrations()
    {
        /** @var \BlendMigrations $migrationLog */
        foreach ($this->blendMigrations as $name => $migrationLog) {
            if (!$this->canRunMigrationVerifyLog($migrationLog)) {
                continue;
            }
            /** @var string $name */
            $name = $migrationLog->get('name');

            // new blender for each instance
            $blender = new Blender($this->modx, $this->blender->getUserInteractionHandler(), $this->blender->getConfig());

            /** @var Migrations $migration */
            $migration = $this->loadMigrationClass($name, $blender);

            if ($migration instanceof Migrations) {
                $this->out('Load Class: '.$name.' M: '.$this->migration_method, OutputInterface::VERBOSITY_DEBUG);

                $migration->{$this->migration_method}();

                $migrationLog->set('ran_sequence', $this->getRanSequence((int)$migrationLog->get('ran_sequence')));
                $migrationLog->set('status', $this->migration_method . '_complete');
                $migrationLog->set('processed_at', date('Y-m-d H:i:s'));
                $migrationLog->save();

            } else {
                // error
                throw new \Exception('Class: ' . $name .' is not an instance of LCI\BLend\Migrations');
            }
        }
    }

    /**
     * @param $loaded_migrations
     */
    protected function runLoadedFileMigrations($loaded_migrations)
    {
        $logged_migrations = $this->getBlendMigrationCollection();

        /** @var Migrations $migration */
        foreach ($loaded_migrations as $name => $migration) {
            if (
                !$this->canRunMigrationVerifyMigration($name, $migration) ||
                (isset($logged_migrations[$name]) && !$this->canRunMigrationVerifyLog($logged_migrations[$name]))
            ) {
                continue;
            }

            $migration->{$this->migration_method}();

            if (isset($logged_migrations[$name])) {
                /** @var \BlendMigrations $migrationLog */
                $migrationLog = $logged_migrations[$name];
                $migrationLog->set('ran_sequence', $this->getRanSequence($migrationLog->get('ran_sequence')));
                $migrationLog->set('status', $this->migration_method . '_complete');
                $migrationLog->set('processed_at', date('Y-m-d H:i:s'));
                $migrationLog->save();

            } else {
                $this->logMigration([
                    'project' => $this->project,
                    'name' => $name,
                    'author' => $migration->getAuthor(),
                    'description' => $migration->getDescription(),
                    'type' => $migration->getType(),
                    'version' => $migration->getVersion(),
                    'status' => $this->migration_method . '_complete',
                    'ran_sequence' => $this->getRanSequence(),
                    'processed_at' => date('Y-m-d H:i:s')
                    ]);

            }
        }
    }

    /**
     * @param string $name
     * @param Migrations $migration
     * @return bool
     */
    protected function canRunMigrationVerifyMigration($name, Migrations $migration)
    {
        $can = true;

        if (!empty($this->migration_name) && $this->migration_name !== $name) {
            $can = false;
        }

        if ($migration->getType() !== $this->migration_type) {
            $can = false;
        }

        return $can;
    }

    /**
     * @param \BlendMigrations $migration
     * @return bool
     */
    protected function canRunMigrationVerifyLog($migration)
    {
        $can = true;

        if ($this->migration_id > 0 && $migration->get('id') != $this->migration_id) {
            $can = false;
        }

        /** @var string $name */
        $name = $migration->get('name');

        if (!empty($this->migration_name) && $this->migration_name !== $name) {
            $can = false;
        }

        /** @var string $status ~ ready|up_complete|down_complete*/
        $status = $migration->get('status');

        /** @var string $server_type */
        $server_type = $migration->get('type');

        if (($this->migration_type != $server_type) ||
            $status == 'seed export' ||
            ($this->migration_method == 'up' && $status == 'up_complete') ||
            ($this->migration_method == 'down' && $status != 'up_complete')
        ) {
            $this->out('canRunMigrationVerifyLog() Failed' . __LINE__, OutputInterface::VERBOSITY_DEBUG);
            $can = false;
        }

        return $can;
    }

    /**
     * @param bool $reload
     *
     * @return array ~ array of \BlendMigrations
     */
    protected function getBlendMigrationCollection($reload = false)
    {
        if (
            $this->blender->isBlendInstalledInModx() &&
            (!$this->blendMigrations || $reload)
        ) {
            $blendMigrations = [];

            $dir = 'ASC';
            if ($this->migration_method == 'down') {
                $dir = 'DESC';
            }

            /** @var \xPDOQuery $query */
            $query = $this->modx->newQuery($this->blender->getBlendClassObject());
            $query->where(['project' => $this->project]);

            if ($this->migration_id > 0) {
                $query->where(['id' => $this->migration_id]);

            } elseif (!empty($this->migration_name)) {
                $query->where(['name' => $this->migration_name]);

            }

            $query
                ->sortby('ran_sequence', $dir)
                ->sortBy('name', $dir);

            if ($this->migration_count > 0) {
                $query->limit($this->migration_count);
            }
            $query->prepare();

            $this->out(__METHOD__ .':: sql: ' . $query->toSQL(), OutputInterface::VERBOSITY_DEBUG);

            $migrationCollection = $this->modx->getCollection($this->blender->getBlendClassObject(), $query);

            /** @var \BlendMigrations $migration */
            foreach ($migrationCollection as $migration) {
                $blendMigrations[$migration->get('name')] = $migration;
            }
            $this->blendMigrations = $blendMigrations;
        }

        return $this->blendMigrations;
    }

    /**
     * @param int $ran_sequence
     * @return int|mixed
     */
    protected function getRanSequence(int $ran_sequence=0)
    {
        if ($this->migration_method == 'up') {
            /** @var \xPDOQuery $query */
            $query = $this->modx->newQuery($this->blender->getBlendClassObject());
            $query
                ->where([
                    'project' => $this->project,
                    'status' => 'up_complete'
                ])
                ->sortby('ran_sequence', 'DESC')
                ->limit(1);

            /** @var \BlendMigrations $lastMigrationLog */
            $lastMigrationLog = $this->modx->getObject($this->blender->getBlendClassObject(), $query);

            if (is_object($lastMigrationLog)) {
                $ran_sequence = $lastMigrationLog->get('ran_sequence');
            }

            // Advance
            ++$ran_sequence;
        }

        return $ran_sequence;
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
     * @param string $message
     * @param int $verbose
     */
    protected function outError($message)
    {
        $this->blender->out($message, true);
    }

    /**
     * @param string $message
     * @param int $verbose
     */
    protected function out($message, $verbose=OutputInterface::VERBOSITY_NORMAL)
    {
        if ($this->verbose >= $verbose) {
            $this->blender->out($message);
        }
    }
}