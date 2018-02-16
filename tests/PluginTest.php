<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LCI\Blend\Blender;
use League\CLImate\CLImate;

final class PluginTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testBlendOneRawPlugin()
    {
        $plugin_name = 'testPlugin1';
        $plugin_description = 'This is my first test  plugin, note this is limited to 255 or something and no HTML';
        $plugin_code = '<?php $eventName = $modx->event->name; ';
        $plugin_event = 'OnUserActivate';

        /** @var \LCI\Blend\Plugin $testPlugin1 */
        $testPlugin1 = $this->blender->blendOneRawPlugin($plugin_name);
        $testPlugin1
            ->setSeedsDir($plugin_name)
            ->setDescription($plugin_description)
            ->setCategoryFromNames('Parent Plugin Cat=>Child Plugin Cat')
            ->setCode($plugin_code, true)
            ->setAsStatic('core/components/mysite/elements/plugins/myPlugin.tpl')
            ->attachOnEvent($plugin_event);

        $blended = $testPlugin1->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $plugin_name.' plugin blend attempted'
        );

        // Validate data:
        if ($blended) {
            /** @var \LCI\Blend\Plugin $blendPlugin */
            $blendPlugin = $testPlugin1->loadCurrentVersion($plugin_name);
            $this->assertInstanceOf(
                '\LCI\Blend\Plugin',
                $blendPlugin,
                'Validate instance was created \LCI\Blend\Plugin'
            );

            if ($blendPlugin instanceof \LCI\Blend\Plugin) {
                $this->assertEquals(
                    $plugin_name,
                    $blendPlugin->getName(),
                    'Compare plugin name'
                );

                $this->assertEquals(
                    $plugin_description,
                    $blendPlugin->getDescription(),
                    'Compare plugin description'
                );

                $this->assertEquals(
                    $this->removePHPtags($plugin_code),
                    $blendPlugin->getCode(),
                    'Compare plugin code'
                );

                // @TODO Broke??
                /* */
                $related = $blendPlugin->getRelatedData();

                $this->assertEquals(
                    $plugin_event,
                    $related[0]['event'],
                    'Compare plugin event'
                );
                /* */

                $this->assertEquals(
                    true,
                    $blendPlugin->revertBlend(),
                    'Revert blend'
                );

            }
        }
    }

    public function testMakePluginSeeds()
    {
        $plugin_name = 'testPlugin2';
        $plugin_description = 'This is my 2nd test plugin, note this is limited to 255 or something and no HTML';
        $plugin_code = '<?php $eventName = $modx->event->name;//2nd ';
        $plugin_event = 'OnWebPageInit';

        // Make test plugin:
        $testPlugin2 = $this->modx->getObject('modPlugin', ['name' => $plugin_name]);
        if (is_object($testPlugin2)) {
            $this->assertEquals(
                false,
                $testPlugin2,
                $plugin_name.' already exists'
            );

        } else {
            $testPlugin2 = $this->modx->newObject('modPlugin');
            $testPlugin2->fromArray([
                'name' => $plugin_name,
                'description' => $plugin_description,
                'content' => $plugin_code
            ]);

            $event = $this->modx->newObject('modPluginEvent');
            $event->fromArray([
                'event' => $plugin_event,
                'priority' => 0,
                'propertyset' => 0
            ]);
            $events = [$event];
            $testPlugin2->addMany($events, 'PluginEvents');
            $testPlugin2->save();
        }

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $this->blender->makePluginSeeds(['name' => $plugin_name]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.$plugin_name.'.migration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationDirectory().'m2018_01_10_093000_Plugin.php'))),
            'Comparing existing testPlugin2 migration file with generated file'
        );

        $fixed_data = require_once BLEND_COMPARE_DIRECTORY.'testPlugin2.seed.php';
        $generated_data = false;
        $seed_file = $this->blender->getSeedsDirectory().BLEND_TEST_SEEDS_DIR.DIRECTORY_SEPARATOR.'elements'.DIRECTORY_SEPARATOR.'modPlugin_testPlugin2.cache.php';
        if (file_exists($seed_file)) {
            $generated_data = require_once $seed_file;
        }
        unset($generated_data['id'], $generated_data['related_data'][0]['pluginid']);

        $this->assertEquals(
            $fixed_data,
            $generated_data,
            'Comparing existing testPlugin2 seed file with generated seed file'
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    public function testCleanUpMakePluginSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $plugin_name = 'testPlugin2';

        if (BLEND_CLEAN_UP) {
            // Remove created test plugin:
            $testPlugin2 = $this->modx->getObject('modPlugin', ['name' => $plugin_name]);
            if (is_object($testPlugin2)) {
                $testPlugin2->remove();

            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'plugin'),
                'Remove created plugin2 migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }


    public function testPluginMigration()
    {
        $migration = 'PluginMigrationExample';
        $plugin_name = 'testPlugin3';
        $plugin_description = 'This is my 3rd test plugin, note this is limited to 255 or something and no HTML';
        $plugin_code = '<?php $eventName = $modx->event->name;//3rd ';

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $testPlugin3 = $this->modx->getObject('modPlugin', ['name' => $plugin_name]);
        $this->assertInstanceOf(
            '\modPlugin',
            $testPlugin3,
            'Validate testPluginMigration that plugin was created '.$plugin_name
        );

        if ($testPlugin3 instanceof \modPlugin) {
            $this->assertEquals(
                $plugin_name,
                $testPlugin3->get('name'),
                'Compare plugin name'
            );

            $this->assertEquals(
                $plugin_description,
                $testPlugin3->get('description'),
                'Compare plugin description'
            );

            $this->assertEquals(
                $this->removePHPtags($plugin_code),
                $testPlugin3->getContent(),
                'Compare plugin code'
            );
        }
    }

    public function testPluginRevertMigration()
    {
        $migration = 'PluginMigrationExample';
        $plugin_name = 'testPlugin3';

        $testPlugin3 = $this->modx->getObject('modPlugin', ['name' => $plugin_name]);

        $this->assertInstanceOf(
            '\modPlugin',
            $testPlugin3,
            'Validate testPluginMigration that plugin was created '.$plugin_name
        );

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $testPlugin4 = $this->modx->getObject('modPlugin', ['name' => $plugin_name]);

        $this->assertEquals(
            false,
            $testPlugin4,
            'Compare testPluginRevertMigration, should be empty/false'
        );
    }

    public function testRemoveBlend()
    {
        if (BLEND_CLEAN_UP) {
            $this->blender->install('down');

            $this->assertEquals(
                false,
                $this->blender->isBlendInstalledInModx()
            );
        }
    }
}
