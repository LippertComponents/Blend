<?php
//declare(strict_types=1);

final class TemplateTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testGetBlendableTemplate()
    {
        $template_name = 'testTemplate1';
        $template_description = 'This is my first test template, note this is limited to 255 or something and no HTML';
        $template_code = '<!DOCTYPE html><html lang="en"><head><title>[[*pagetitle]]</title></head><body>[[*content]]</body></html>';

        /** @var \LCI\Blend\Blendable\Template $testTemplate1 */
        $testTemplate1 = $this->blender->getBlendableTemplate($template_name);
        $testTemplate1
            ->setSeedsDir($template_name)
            ->setFieldDescription($template_description)
            ->setFieldCategory('Parent Template Cat=>Child Template Cat')
            ->setFieldCode($template_code, true)
            ->setAsStatic('core/components/mysite/elements/templates/myTemplate.tpl');
        // @TODO add a TV here
        //->attachTemplateVariable('testTemplateVariable1');

        $blended = $testTemplate1->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $template_name.' template blend attempted'
        );

        // Validate data:
        if ($blended) {
            /** @var \LCI\Blend\Blendable\Template $blendTemplate */
            $blendTemplate = $testTemplate1->getCurrentVersion();
            $this->assertInstanceOf(
                '\LCI\Blend\Blendable\Template',
                $blendTemplate,
                'Validate instance was created \LCI\Blend\Template'
            );

            if ($blendTemplate instanceof \LCI\Blend\Blendable\Template) {
                $this->assertEquals(
                    $template_name,
                    $blendTemplate->getFieldName(),
                    'Compare template name'
                );

                $this->assertEquals(
                    $template_description,
                    $blendTemplate->getFieldDescription(),
                    'Compare template description'
                );

                $this->assertEquals(
                    $template_code,
                    $blendTemplate->getFieldCode(),
                    'Compare template code'
                );

                $this->assertEquals(
                    true,
                    $blendTemplate->revertBlend(),
                    'Revert blend'
                );

            }
        }
    }

    public function testMakeTemplateSeeds()
    {
        $template_name = 'testTemplate2';
        $template_description = 'This is my 2nd test template, note this is limited to 255 or something and no HTML';
        $template_code = '<!DOCTYPE html><html lang="en"><head><title>[[*pagetitle]]</title></head><body><!-- 2nd -->[[*content]]</body></html>';
        // @TODO add tvs here

        // Make test template:
        $testTemplate2 = $this->modx->getObject('modTemplate', ['templatename' => $template_name]);
        if (is_object($testTemplate2)) {
            $this->assertEquals(
                false,
                $testTemplate2,
                $template_name.' already exists'
            );

        } else {
            $testTemplate2 = $this->modx->newObject('modTemplate');
            $testTemplate2->fromArray([
                'templatename' => $template_name,
                'description' => $template_description,
                'content' => $template_code
            ]);
            $testTemplate2->save();
        }

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seeds_directory = $this->blender->getMigrationName('template');

        $this->blender->getSeedMaker()->makeTemplateSeeds(['templatename' => $template_name]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.$template_name.'.migration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().'m2018_01_10_093000_Template.php'))),
            'Comparing existing testTemplate2 migration file with generated file'
        );

        $fixed_data = require_once BLEND_COMPARE_DIRECTORY.'testTemplate2.seed.php';
        $generated_data = false;
        $seed_file = $this->blender->getSeedsPath($seeds_directory) . 'elements' . DIRECTORY_SEPARATOR . 'templates'. DIRECTORY_SEPARATOR .$template_name.'.cache.php';
        if (file_exists($seed_file)) {
            $generated_data = require_once $seed_file;
        }
        unset($generated_data['id']);

        $this->assertEquals(
            $fixed_data,
            $generated_data,
            'Comparing existing testTemplate2 seed file with generated seed file'
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testMakeTemplateSeeds
     */
    public function testCleanUpMakeTemplateSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $template_name = 'testTemplate2';

        if (BLEND_CLEAN_UP) {
            // Remove created test template:
            $testTemplate2 = $this->modx->getObject('modTemplate', ['templatename' => $template_name]);
            if (is_object($testTemplate2)) {
                $testTemplate2->remove();

            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'template'),
                'Remove created template2 migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }


    public function testTemplateMigration()
    {
        $migration = 'TemplateMigrationExample';
        $template_name = 'testTemplate3';
        $template_description = 'This is my 3rd test template, note this is limited to 255 or something and no HTML';
        $template_code = '<!DOCTYPE html><html lang="en"><head><title>[[*pagetitle]]</title></head><body><!-- 3rd -->[[*content]]</body></html>';

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $testTemplate3 = $this->modx->getObject('modTemplate', ['templatename' => $template_name]);
        $this->assertInstanceOf(
            '\modTemplate',
            $testTemplate3,
            'Validate testTemplateMigration that template was created '.$template_name
        );

        if ($testTemplate3 instanceof \modTemplate) {
            $this->assertEquals(
                $template_name,
                $testTemplate3->get('templatename'),
                'Compare template name'
            );

            $this->assertEquals(
                $template_description,
                $testTemplate3->get('description'),
                'Compare template description'
            );

            $this->assertEquals(
                $template_code,
                $testTemplate3->getContent(),
                'Compare template code'
            );
        }
    }

    /**
     * @depends testTemplateMigration
     */
    public function testTemplateRevertMigration()
    {
        $migration = 'TemplateMigrationExample';
        $template_name = 'testTemplate3';

        $testTemplate3 = $this->modx->getObject('modTemplate', ['templatename' => $template_name]);

        $this->assertInstanceOf(
            '\modTemplate',
            $testTemplate3,
            'Validate testTemplateMigration that template was created '.$template_name
        );

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $testTemplate4 = $this->modx->getObject('modTemplate', ['templatename' => $template_name]);

        $this->assertEquals(
            false,
            $testTemplate4,
            'Compare testTemplateRevertMigration, should be empty/false'
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
