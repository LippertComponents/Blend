<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LCI\Blend\Blender;
use League\CLImate\CLImate;

final class SnippetTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testBlendOneRawSnippet()
    {
        $snippet_name = 'testSnippet1';
        $snippet_description = 'This is my first test  snippet, note this is limited to 255 or something and no HTML';
        $snippet_code = '<?php return \'This is a test Snippet!\'; ';
        /** @var \LCI\Blend\Snippet $testSnippet1 */
        $testSnippet1 = $this->blender->blendOneRawSnippet($snippet_name);
        $testSnippet1
            ->setSeedsDir($snippet_name)
            ->setDescription($snippet_description)
            ->setCategoryFromNames('Parent Cat=>Child Cat')
            ->setCode($snippet_code)
            ->setAsStatic('core/components/mysite/elements/snippets/mySnippet.tpl');

        $blended = $testSnippet1->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $snippet_name.' snippet blend attempted'
        );

        // Validate data:
        if ($blended) {
            /** @var \LCI\Blend\Snippet $blendSnippet */
            $blendSnippet = $testSnippet1->loadCurrentVersion($snippet_name);
            $this->assertInstanceOf(
                '\LCI\Blend\Snippet',
                $blendSnippet,
                'Validate instance was created \LCI\Blend\Snippet'
            );

            if ($blendSnippet instanceof \LCI\Blend\Snippet) {
                $this->assertEquals(
                    $snippet_name,
                    $blendSnippet->getName(),
                    'Compare snippet name'
                );

                $this->assertEquals(
                    $snippet_description,
                    $blendSnippet->getDescription(),
                    'Compare snippet description'
                );

                $this->assertEquals(
                    $this->removePHPtags($snippet_code),
                    $blendSnippet->getCode(),
                    'Compare snippet code'
                );

                $this->assertEquals(
                    true,
                    $blendSnippet->revertBlend(),
                    'Revert blend'
                );
            }
        }
    }

    public function testMakeSnippetSeeds()
    {
        $snippet_name = 'testSnippet2';
        $snippet_description = 'This is my 2nd test snippet, note this is limited to 255 or something and no HTML';
        $snippet_code = '<?php return \'This is the 2nd test Snippet!\'; ';

        // Make test snippet:
        $testSnippet2 = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);
        if (is_object($testSnippet2)) {
            $this->assertEquals(
                false,
                $testSnippet2,
                $snippet_name.' already exists'
            );

        } else {
            $testSnippet2 = $this->modx->newObject('modSnippet');
            $testSnippet2->fromArray([
                'name' => $snippet_name,
                'description' => $snippet_description,
                'content' => $snippet_code
            ]);
            $testSnippet2->save();
        }

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $this->blender->makeSnippetSeeds(['name' => $snippet_name]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.$snippet_name.'.migration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationDirectory().'m2018_01_10_093000_Snippet.php'))),
            'Comparing existing testSnippet2 migration file with generated file'
        );

        $fixed_data = require_once BLEND_COMPARE_DIRECTORY.'testSnippet2.seed.php';
        $generated_data = false;
        $seed_file = $this->blender->getSeedsDirectory().BLEND_TEST_SEEDS_DIR.DIRECTORY_SEPARATOR.'elements'.DIRECTORY_SEPARATOR.'modSnippet_testSnippet2.cache.php';
        if (file_exists($seed_file)) {
            $generated_data = require_once $seed_file;
        }
        unset($generated_data['id']);

        $this->assertEquals(
            $fixed_data,
            $generated_data,
            'Comparing existing testSnippet2 seed file with generated seed file'
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    public function testCleanUpMakeSnippetSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $snippet_name = 'testSnippet2';

        if (BLEND_CLEAN_UP) {
            // Remove created test snippet:
            $testSnippet2 = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);
            if (is_object($testSnippet2)) {
                $testSnippet2->remove();

            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'snippet'),
                'Remove created snippet2 migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }


    public function testSnippetMigration()
    {
        $migration = 'SnippetMigrationExample';
        $snippet_name = 'testSnippet3';
        $snippet_description = 'This is my 3rd test snippet, note this is limited to 255 or something and no HTML';
        $snippet_code = '<?php return \'This is the 3rd test Snippet!\'; ';

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $testSnippet3 = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);
        $this->assertInstanceOf(
            '\modSnippet',
            $testSnippet3,
            'Validate testSnippetMigration that snippet was created '.$snippet_name
        );

        if ($testSnippet3 instanceof \modSnippet) {
            $this->assertEquals(
                $snippet_name,
                $testSnippet3->get('name'),
                'Compare snippet name'
            );

            $this->assertEquals(
                $snippet_description,
                $testSnippet3->get('description'),
                'Compare snippet description'
            );

            $this->assertEquals(
                $this->removePHPtags($snippet_code),
                $testSnippet3->getContent(),
                'Compare snippet code'
            );
        }
    }

    public function testSnippetRevertMigration()
    {
        $migration = 'SnippetMigrationExample';
        $snippet_name = 'testSnippet3';

        $testSnippet3 = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);

        $this->assertInstanceOf(
            '\modSnippet',
            $testSnippet3,
            'Validate testSnippetMigration that snippet was created '.$snippet_name
        );

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $testSnippet4 = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);

        $this->assertEquals(
            false,
            $testSnippet4,
            'Compare testSnippetRevertMigration, should be empty/false'
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
