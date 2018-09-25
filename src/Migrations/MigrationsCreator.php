<?php

namespace LCI\Blend\Migrations;

use LCI\Blend\Helpers\Format;
use LCI\MODX\Console\Helpers\UserInteractionHandler;

/**
 * Class MigrationsCreator ~ will create Migrations
 * @package xPDO\Migrations
 */
class MigrationsCreator
{
    /** @var array  */
    protected $class_data = [];

    /** @var string */
    protected $description;

    /** @var string */
    protected $name;

    /** @var array  */
    protected $log_data = [];

    /** @var array  */
    protected $placeholders = [];

    /** @var string  */
    protected $server_type = 'master';

    /** @var false|string  */
    protected $path_time_stamp;

    /** @var string  */
    protected $migration_template = 'blank.txt';

    /** @var string ~ 1.0.0 the version of the migration file or related project */
    protected $version = '';

    /** @var Format */
    protected $format;

    protected $migration_templates_path;
    protected $migrations_path;
    protected $seeds_path;

    /** @var \LCI\MODX\Console\Helpers\UserInteractionHandler */
    protected $userInteractionHandler;

    /**
     * MigrationsCreator constructor.
     * @param UserInteractionHandler $userInteractionHandler
     * @param array $class_data
     */
    public function __construct(UserInteractionHandler $userInteractionHandler, $class_data = [])
    {
        $this->class_data = $class_data;

        $this->userInteractionHandler = $userInteractionHandler;

        $this->path_time_stamp = date('Y_m_d_His');
        $this->format = new Format($this->path_time_stamp);

        $this->migration_templates_path = __DIR__.'/templates/';
        $this->setBaseMigrationsPath(dirname(__DIR__).DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path ~ like /var/www/public/core/components/blend/
     * @param bool $append ~ if true will create database/migrations with in the path
     * @return $this
     */
    public function setMigrationsPath($path, $append = false)
    {
        $this->migrations_path = rtrim($path, '\/\\') . DIRECTORY_SEPARATOR;

        if (file_exists($path) && $append) {
            if (!file_exists($path.'database')) {
                mkdir($path.'database');
            }
            if (!file_exists($path.'database/migrations')) {
                mkdir($path.'database/migrations');
            }
            $this->migrations_path = $path.'database/migrations/';

        } elseif (!file_exists($path) && !$append) {
            // @TODO review:
            mkdir($path, 0777, true);
        }

        return $this;
    }

    /**
     * @param string $path ~ like /var/www/public/core/components/blend/
     * @param bool $append ~ if true will create database/seeds with in the path
     * @return $this
     */
    public function setSeedsPath($path, $append = false)
    {
        $this->seeds_path = rtrim($path, '\/\\') . DIRECTORY_SEPARATOR;

        if (file_exists($path) && $append) {
            if (!file_exists($path.'database')) {
                mkdir($path.'database');
            }
            if (!file_exists($path.'database/seeds')) {
                mkdir($path.'database/seeds');
            }
            $this->seeds_path = $path.'database/seeds/';

        } elseif (!file_exists($path) && !$append) {
            // @TODO review:
            mkdir($path, 0777, true);
        }

        return $this;
    }

    /**
     * @param string $path ~ like /var/www/public/core/components/blend/ And then will create database/migrations and
     *      database/seeds
     * @return $this
     */
    public function setBaseMigrationsPath($path)
    {
        $this->setMigrationsPath($path, true);
        $this->setSeedsPath($path, true);
        return $this;
    }
    /**
     * @return bool
     */
    public function createBlankMigrationClassFile()
    {
        return $this->writeMigrationClassFile('blank');
    }

    /**
     * @return bool
     */
    public function createChunkMigrationClassFile()
    {
        $this->migration_template = 'chunk.txt';
        $this->placeholders['chunkData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManyChunks($this->chunks, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManyChunks($this->chunks, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    /**
     * @return bool
     */
    public function createContextMigrationClassFile()
    {
        $this->migration_template = 'context.txt';
        $this->placeholders['contextData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManyContexts($this->contexts, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManyContexts($this->contexts, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    /**
     * @return bool
     */
    public function createMediaSourceMigrationClassFile()
    {
        $this->migration_template = 'mediaSource.txt';
        $this->placeholders['mediaSourceData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManyMediaSources($this->media_sources, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManyMediaSources($this->media_sources, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    /**
     * @return bool
     */
    public function createPluginMigrationClassFile()
    {
        $this->migration_template = 'plugin.txt';
        $this->placeholders['pluginData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManyPlugins($this->plugins, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManyPlugins($this->plugins, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    /**
     * @return bool
     */
    public function createResourceMigrationClassFile()
    {
        $this->migration_template = 'resource.txt';

        $this->placeholders['resourceData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManyResources($this->resources, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManyResources($this->resources, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    /**
     * @return bool
     */
    public function createSnippetMigrationClassFile()
    {
        $this->migration_template = 'snippet.txt';
        $this->placeholders['snippetData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManySnippets($this->snippets, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManySnippets($this->snippets, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    /**
     * @return bool
     */
    public function createSystemSettingsMigrationClassFile()
    {
        $this->migration_template = 'systemSettings.txt';
        $this->placeholders['settingsData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManySystemSettings($this->settings, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManySystemSettings($this->settings, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    public function createTemplateMigrationClassFile()
    {
        $this->migration_template = 'template.txt';
        $this->placeholders['templateData'] = $this->format->prettyVarExport($this->class_data);
        $this->placeholders['classUpInners'] = '$this->blender->blendManyTemplates($this->templates, $this->getSeedsDir());';
        $this->placeholders['classDownInners'] = '$this->blender->revertBlendManyTemplates($this->templates, $this->getSeedsDir());';

        return $this->writeMigrationClassFile();
    }

    /**
     * @return bool
     */
    public function createSiteMigrationClassFile()
    {
        $this->migration_template = 'site.txt';
        $this->placeholders['siteData'] = $this->format->prettyVarExport($this->class_data);

        return $this->writeMigrationClassFile();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getLogData()
    {
        return $this->log_data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return false|string
     */
    public function getPathTimeStamp()
    {
        return $this->path_time_stamp;
    }

    /**
     * @return string
     */
    public function getServerType()
    {
        return $this->server_type;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }


    /**
     * @param string $description
     * @return MigrationsCreator
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param bool $log
     * @return MigrationsCreator
     */
    public function setLog($log)
    {
        $this->log = $log;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param false|string $path_time_stamp
     * @return MigrationsCreator
     */
    public function setPathTimeStamp($path_time_stamp)
    {
        $this->format = new Format($path_time_stamp);
        $this->path_time_stamp = $path_time_stamp;
        return $this;
    }

    /**
     * @param string $server_type
     * @return MigrationsCreator
     */
    public function setServerType($server_type)
    {
        $this->server_type = $server_type;
        return $this;
    }

    /**
     * @param string $version
     * @return MigrationsCreator
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return bool
     */
    protected function writeMigrationClassFile()
    {
        $class_name = $this->format->getMigrationName(substr($this->migration_template, 0, -4), $this->name);

        $placeholders = array_merge(
            [
                'classCreateDate' => date('Y/m/d'),
                'classCreateTime' => date('G:i:s T P'),
                'className' => $class_name,
                'classUpInners' => '//@TODO',
                'classDownInners' => '//@TODO',
                'description' => $this->getDescription(),
                'serverType' => $this->getServerType(),
                'seeds_dir' => $class_name,
                'version' => $this->getVersion()
            ],
            $this->placeholders
        );

        $file_contents = '';

        $migration_template = $this->migration_templates_path.$this->migration_template;
        if (file_exists($migration_template)) {
            $file_contents = file_get_contents($migration_template);
        } else {
            $this->out('Migration template file not found: '.$migration_template, true);
        }

        foreach ($placeholders as $name => $value) {
            $file_contents = str_replace('[[+'.$name.']]', $value, $file_contents);
        }

        $this->out($this->migrations_path.$class_name.'.php');

        $write = false;
        if (file_exists($this->migrations_path.$class_name.'.php')) {
            $this->out($this->migrations_path.$class_name.'.php migration file already exists', true);

        }
        /**
         * @TODO refactor this?
         elseif (is_object($this->xPDO->getObject($this->migrator->getMigrationClassObject(), ['name' => $class_name]))) {
            $this->out($class_name . ' migration already has been created in the xpdo_migrations table', true);

        } */
        else {
            try {
                $write = file_put_contents($this->migrations_path.$class_name.'.php', $file_contents);
                 $this->log_data = [
                    'name' => $class_name,
                    'type' => $this->getServerType(),
                    'description' => $this->getDescription(),
                    'version' => $this->getVersion(),
                    'status' => 'seed export',
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } catch (\Exception $exception) {
                $this->out($exception->getMessage(), true);
            }

            if (!$write) {
                $this->out($this->migrations_path.$class_name.'.php Did not write to file', true);
                $this->out('Verify that the folders exists and are writable by PHP', true);
            }
        }

        return $write;
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

}