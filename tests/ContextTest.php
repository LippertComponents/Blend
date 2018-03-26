<?php
//declare(strict_types=1);

final class ContextTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testGetBlendableContext()
    {
        $context_key = 'es';
        $description = 'Spanish Context';

        /** @var \LCI\Blend\Blendable\Context $testContext1 */
        $testContext1 = $this->blender->getBlendableContext($context_key);
        $testContext1
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldDescription($description)
            ->setFieldName('Spanish')
            ->setFieldRank(1)
            ->addSetting('my.setting', '1')
            ->addSetting('my.otherSetting', 'Important value!');

        $blended = $testContext1->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $context_key.' context blend attempted'
        );

        // Validate data:
        if ($blended) {
            /** @var \LCI\Blend\Blendable\Context $blendContext */
            $blendContext = $testContext1->getCurrentVersion();
            $this->assertInstanceOf(
                '\LCI\Blend\Blendable\Context',
                $blendContext,
                'Validate instance was created \LCI\Blend\Blendable\Context'
            );

            if ($blendContext instanceof \LCI\Blend\Blendable\Context) {
                $this->assertEquals(
                    $context_key,
                    $blendContext->getFieldKey(),
                    'Compare context name'
                );

                $this->assertEquals(
                    $description,
                    $blendContext->getFieldDescription(),
                    'Compare context description'
                );
            }

            // settings:
            $related_data = $blendContext->getRelatedData();
            $count = 0;
            foreach ($related_data['settings'] as $setting) {
                switch ($setting['key']) {
                    case 'my.setting':
                        $count++;
                        $this->assertEquals(
                            1,
                            $setting['value'],
                            'Compare context setting value'
                        );
                        break;

                    case 'my.otherSetting':
                        $count++;
                        $this->assertEquals(
                            'Important value!',
                            $setting['value'],
                            'Compare context setting value'
                        );
                        break;
                }

            }

            $this->assertEquals(
                2,
                $count,
                'Compare number of context settings created'
            );
        }
    }

    /**
     * @depends testGetBlendableContext
     */
    public function testRevertContext()
    {
        $context_key = 'es';

        /** @var \LCI\Blend\Blendable\Context $blendableContext */
        $blendableContext = $this->blender->getBlendableContext($context_key);
        $blendableContext
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $this->assertEquals(
            true,
            $blendableContext->revertBlend(),
            'Revert blend'
        );
    }

    /**
     * @depends testGetBlendableContext
     */
    public function testMakeContextSeeds()
    {
        $context_key = 'fr';
        $description = 'French Context';

        /** @var \LCI\Blend\Blendable\Context $testContext2 */
        $testContext2 = $this->blender->getBlendableContext($context_key);
        $testContext2
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldDescription($description)
            ->setFieldName('French')
            ->setFieldRank(2)
            ->addSetting('my.setting2', '2')
            ->addSetting('my.otherSetting2', 'Important value2!');

        $blended = $testContext2->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $context_key.' context blend attempted'
        );

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seeds_directory = $this->blender->getMigrationName('context');

        $this->blender->makeContextSeeds(['key' => $context_key]);

        $this->blender->out('DIR: '.BLEND_COMPARE_DIRECTORY.$context_key.'.php', true);
        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.'testContextFR.migration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().'m2018_01_10_093000_Context.php'))),
            'Comparing existing testContext migration file with generated file'
        );

        $fixed_data = require_once BLEND_COMPARE_DIRECTORY.'testContextFR.seed.php';
        $generated_data = false;
        $seed_file = $this->blender->getSeedsPath($seeds_directory).DIRECTORY_SEPARATOR.'contexts'.DIRECTORY_SEPARATOR.$context_key.'.cache.php';
        if (file_exists($seed_file)) {
            $generated_data = require_once $seed_file;
        }
        unset($generated_data['columns']['id']);

        $this->assertEquals(
            $fixed_data,
            $generated_data,
            'Comparing existing testContext seed file with generated seed file'
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testMakeContextSeeds
     */
    public function testCleanUpMakeContextSeeds()
    {
        $context_key = 'fr';
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        if (BLEND_CLEAN_UP) {
            // Remove created test context:
            $this->blender->revertBlendManyContexts(['key' => $context_key]);

            $this->assertEquals(
                false,
                $this->modx->getObject('modContext', ['key' => $context_key]),
                'Remove created '.$context_key.' migration seed file'
            );

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'context'),
                'Remove created '.$context_key.' migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }

    public function testContextMigration()
    {
        $migration = 'ContextMigrationExample';
        $context_key = 'it';
        $name = 'Italian';
        $description = 'Italian language';

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $testContext3 = $this->modx->getObject('modContext', ['key' => $context_key]);
        $this->assertInstanceOf(
            '\modContext',
            $testContext3,
            'Validate ContextMigrationExample that context was created '.$context_key
        );

        if ($testContext3 instanceof \modContext) {
            $this->assertEquals(
                $context_key,
                $testContext3->get('key'),
                'Compare context key'
            );

            $this->assertEquals(
                $name,
                $testContext3->get('name'),
                'Compare context name'
            );

            $this->assertEquals(
                $description,
                $testContext3->get('description'),
                'Compare context description'
            );

            // Test setting values
            $settings = [
                // key => value
                'cultureKey' => 'it',
                'http_host' => 'mysite.com',
                'base_url' => '/it/',
                'site_url' => 'https://mysite.com/it/'
            ];
            /** @var array of \modContextSetting $contextSettings */
            $contextSettings = $testContext3->getMany('ContextSettings');

            /** @var \modContextSetting $setting */
            foreach ($contextSettings as $setting) {
                $value = false;
                if (isset($settings[$setting->get('key')])) {
                    $value = $settings[$setting->get('key')];
                }

                $this->assertEquals(
                    $value,
                    $setting->get('value'),
                    'Compare context setting value for key: '.$setting->get('key')
                );
            }
        }
    }

    /**
     * @depends testContextMigration
     */
    public function testContextRevertMigration()
    {
        $migration = 'ContextMigrationExample';
        $context_key = 'it';

        $textContext3 = $this->modx->getObject('modContext', ['key' => $context_key]);

        $this->assertInstanceOf(
            '\modContext',
            $textContext3,
            'Validate ContextMigrationExample created '.$context_key
        );

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $context = $this->modx->getObject('modContext', ['key' => $context_key]);

        $this->assertEquals(
            false,
            $context,
            'Compare ContextMigrationExample revert, should be empty/false'
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
