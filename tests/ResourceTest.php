<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LCI\Blend\Blender;
use League\CLImate\CLImate;

final class ResourceTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testBlendManyResources()
    {
        $resource_seeds = [
            'test-blend-many-resource-1',
            'test-blend-many-resource-2'
        ];

        $this->assertEquals(
            true,
            $this->blender->blendManyResources($resource_seeds, BLEND_TEST_TIMESTAMP),
            'testBlendManyResources() blend attempted'
        );

        // verify that they exist:
        foreach ($resource_seeds as $count => $seed) {
            $testResource = $this->modx->getObject('modResource', ['alias' => $seed]);
            $this->assertInstanceOf(
                '\modResource',
                $testResource,
                'Verifying that '.$seed.' was created'
            );
        }
    }

    public function testRevertBlendManyResources()
    {
        $resource_seeds = [
            'test-blend-many-resource-1',
            'test-blend-many-resource-2'
        ];

        $this->assertEquals(
            true,
            $this->blender->revertBlendManyResources($resource_seeds, BLEND_TEST_TIMESTAMP),
            'testBlendManyResources() blend attempted'
        );

        // verify that they exist:
        foreach ($resource_seeds as $count => $seed) {
            $testResource = $this->modx->getObject('modResource', ['alias' => $seed]);
            $this->assertEquals(
                false,
                $testResource,
                'Verifying that '.$seed.' was removed/reverted'
            );
        }
    }

    public function testMakeResourceSeeds()
    {
        $test_resources = [
            'test-blend-many-resource-3' => [
                'pagetitle' => 'Blend Resource Test 3',
                'content' => 'Content #3 goes here'
            ],
            'test-blend-many-resource-4' => [
                'pagetitle' => 'Blend Resource Test 4',
                'content' => 'Content #4 goes here'
            ],
        ];

        $aliases = [];
        foreach ($test_resources as $alias => $test_resource) {
            // Make test resource:
            $testResource2 = $this->modx->getObject('modResource', ['alias' => $alias]);
            if (is_object($testResource2)) {
                $this->assertEquals(
                    false,
                    $testResource2,
                    $alias . ' resource already exists'
                );

            } else {
                $aliases[] = $alias;

                $testResource2 = $this->modx->newObject('modResource');
                $testResource2->fromArray($test_resource);
                $testResource2->set('alias', $alias);
                $testResource2->save();
            }
        }

        $actual_timestamp = $this->blender->getTimestamp();
        $this->blender->setTimestamp(BLEND_TEST_TIMESTAMP);

        $seeds = $this->blender->makeResourceSeeds(['alias:IN' => $aliases]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.'testResource2.migration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationDirectory().'m2018_01_10_093000_Resource.php'))),
            'Comparing existing testResource2 migration file with generated file'
        );

        $count = 3;
        foreach ($test_resources as $alias => $test_resource) {
            $fixed_data = require_once BLEND_COMPARE_DIRECTORY . 'testResource'.$count.'.seed.php';
            $generated_data = false;
            $seed_file = $this->blender->getSeedsDirectory() . BLEND_TEST_TIMESTAMP . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $alias.'.cache.php';
            if (file_exists($seed_file)) {
                $generated_data = require_once $seed_file;
            }
            unset($generated_data['id'], $generated_data['createdon']);

            $this->assertEquals(
                $fixed_data,
                $generated_data,
                'Comparing existing testResource'.$count++.' seed file with generated seed file'
            );
        }

        $this->blender->setTimestamp($actual_timestamp);
    }

    public function testCleanUpMakeResourceSeeds()
    {
        $actual_timestamp = $this->blender->getTimestamp();
        $this->blender->setTimestamp(BLEND_TEST_TIMESTAMP);

        $aliases = [
            'test-blend-many-resource-3',
            'test-blend-many-resource-4'
        ];

        if (BLEND_CLEAN_UP) {
            // Remove created test resources:
            $testResources = $this->modx->getCollection('modResource', ['alias:IN' => $aliases]);
            foreach ($testResources as $testResource) {
                $testResource->remove();
            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'resource'),
                'Remove created resource2 migration seed file'
            );
        }
        $this->blender->setTimestamp($actual_timestamp);
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
