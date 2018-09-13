<?php

namespace LCI\Blend;

use Exception;
use LCI\Blend\Blendable\Chunk;
use LCI\Blend\Blendable\Context;
use LCI\Blend\Blendable\MediaSource;
use LCI\Blend\Blendable\Plugin;
use LCI\Blend\Blendable\Resource;
use LCI\Blend\Blendable\Snippet;
use LCI\Blend\Blendable\Template;
use LCI\Blend\Helpers\Format;
use LCI\Blend\Migrations\MigrationsCreator;
use LCI\Blend\Model\xPDO\BlendMigrations;

/**
 * Class SeedMaker
 * @package LCI\Blend
 */
class SeedMaker
{
    /** @var  \modx */
    protected $modx;

    /** @var Blender */
    protected $blender;

    protected $format;

    /**
     * SeedMaker constructor.
     * @param \modx $modx
     * @param Blender $blender
     */
    public function __construct(\modx $modx, Blender $blender)
    {
        $this->modx = $modx;
        $this->blender = $blender;

        $this->format = new Format($this->blender->getSeedsDir());
    }

// Find: ->makeChunkSeeds( Replace: ->getSeedMaker()->makeChunkSeeds(
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
            $blendChunk = new Chunk($this->modx, $this->blender, $chunk->get('name'));
            $seed_key = $blendChunk
                ->setSeedsDir($this->format->getMigrationName('chunk', $name))
                ->seed();
            $this->blender->out("Chunk: ".$chunk->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $keys);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createChunkMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
            $blendContext = new Context($this->modx, $this->blender, $context->get('key'));
            $seed_key = $blendContext
                ->setSeedsDir($this->format->getMigrationName('context', $name))
                ->seed();
            $this->blender->out("Context: ".$context->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $keys);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createContextMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
            $blendMediaSource = new MediaSource($this->modx, $this->blender, $mediaSource->get('name'));
            $seed_key = $blendMediaSource
                ->setSeedsDir($this->format->getMigrationName('mediaSource', $name))
                ->seed();
            $this->blender->out("Media Source: ".$mediaSource->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $keys);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createMediaSourceMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
            $blendPlugin = new Plugin($this->modx, $this->blender, $plugin->get('name'));
            $seed_key = $blendPlugin
                ->setSeedsDir($this->format->getMigrationName('plugin', $name))
                ->seed();
            $this->blender->out("Plugin: ".$plugin->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $keys);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createPluginMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
            $blendResource = new Resource($this->modx, $this->blender, $resource->get('alias'), $resource->get('context_key'));
            $seed_key = $blendResource
                ->setSeedsDir($this->format->getMigrationName('resource', $name))
                ->seed($resource);
            $this->blender->out("ID: ".$resource->get('id').' Key: '.$seed_key);

            if (!isset($keys[$resource->get('context_key')])) {
                $keys[$resource->get('context_key')] = [];
            }

            $keys[$resource->get('context_key')][] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $keys);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createResourceMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
            $blendSnippet = new Snippet($this->modx, $this->blender, $snippet->get('name'));
            $seed_key = $blendSnippet
                ->setSeedsDir($this->format->getMigrationName('snippet', $name))
                ->seed();
            $this->blender->out("Snippet: ".$snippet->get('name').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $keys);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createSnippetMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
            $blendableSetting = $this->blender->getBlendableSystemSetting($setting->get('key'));
            $setting_data[] = $blendableSetting->seedToArray();
        }

        // https://docs.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.invokeevent
        $this->modx->invokeEvent(
            'OnBlendSeedSystemSettings',
            [
                'blender' => $this->blender,
                'data' => &$setting_data
            ]
        );

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $setting_data);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createSystemSettingsMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
            $blendTemplate = new Template($this->modx, $this->blender, $template->get('templatename'));
            $seed_key = $blendTemplate
                ->setSeedsDir($this->format->getMigrationName('template', $name))
                ->seed();
            $this->blender->out("Template ID: ".$template->get('id').' Key: '.$seed_key);
            $keys[] = $seed_key;
        }

        if ($create_migration_file) {
            /** @var MigrationsCreator $migrationCreator */
            $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $keys);

            $migrationCreator
                ->setPathTimeStamp($this->format->getPathTimeStamp())
                ->setName($name)
                ->setDescription('')
                ->setServerType($server_type)
                ->setMigrationsPath($this->blender->getMigrationPath())
                ->createTemplateMigrationClassFile();

            $this->logCreatedSeedMigration($migrationCreator->getLogData());
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
        $migrationCreator = new MigrationsCreator($this->blender->getUserInteractionHandler(), $site_data);

        $migrationCreator
            ->setPathTimeStamp($this->format->getPathTimeStamp())
            ->setName($name)
            ->setDescription('')
            ->setServerType($server_type)
            ->setMigrationsPath($this->blender->getMigrationPath())
            ->createSiteMigrationClassFile();

        $this->logCreatedSeedMigration($migrationCreator->getLogData());
    }

    /**
     * @param array $data
     */
    protected function logCreatedSeedMigration($data=[])
    {
        try {
            /** @var BlendMigrations $migration */
            $migration = $this->modx->newObject($this->blender->getBlendClassObject());
            if ($migration) {
                $migration->fromArray($data);
                $migration->save();
            }
        } catch (Exception $exception) {
            $this->blender->out($exception->getMessage(), true);
        }
    }
}