<?php
//declare(strict_types=1);

final class ResourceTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testGetBlendableResource()
    {
        //$this->modx->loadClass('sources.modMediaSource');

        $alias = 'blendable-resource';
        $content = 'Content, can put in HTML here';
        $description = 'This is description, don\'t put in HTML here';
        $long_title = 'Long title';
        $page_title = 'Page Title';


        /** @var \LCI\Blend\Blendable\Resource $blendableResource */
        $blendableResource = $this->blender->getBlendableResource($alias);
        $blendableResource
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldContent($content)
            ->setFieldDescription($description)
            ->setFieldLongtitle($long_title)
            ->setFieldPagetitle($page_title);

        $blended = $blendableResource->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $alias.' resource blend attempted'
        );

        // Validate data/convenience methods:
        if ($blended) {
            /** @var \LCI\Blend\Blendable\Resource $blendResource */
            $blendResource = $this->blender->getBlendableResource($alias);
            $this->assertInstanceOf(
                '\LCI\Blend\Blendable\Resource',
                $blendResource,
                'Validate instance was created \LCI\Blend\Blendable\Resource'
            );

            if ($blendResource instanceof \LCI\Blend\Blendable\Resource) {
                $this->assertEquals(
                    $alias,
                    $blendResource->getFieldAlias(),
                    'Compare resource alias'
                );

                $this->assertEquals(
                    $content,
                    $blendResource->getFieldContent(),
                    'Compare content'
                );

                $this->assertEquals(
                    $description,
                    $blendResource->getFieldDescription(),
                    'Compare description'
                );

                $this->assertEquals(
                    $long_title,
                    $blendResource->getFieldLongtitle(),
                    'Compare long title'
                );

                $this->assertEquals(
                    $page_title,
                    $blendResource->getFieldPagetitle(),
                    'Compare page title'
                );
            }
        }
    }

    /**
     * @depends testGetBlendableResource
     */
    public function testRevertGetBlendableResource()
    {
        //$this->modx->loadClass('sources.modMediaSource');

        $alias = 'blendable-resource';

        /** @var \LCI\Blend\Blendable\Resource $blendableResource */
        $blendableResource = $this->blender->getBlendableResource($alias);
        $blendableResource->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $this->assertEquals(
            true,
            $blendableResource->revertBlend(),
            $alias.' resource revertBlend attempted'
        );
    }


    public function testBlendManyResources()
    {
        $resource_seeds = [
            'web' => [
                'test-blend-many-resource-1',
                'test-blend-many-resource-2'
            ]
        ];

        $this->assertEquals(
            true,
            $this->blender->blendManyResources($resource_seeds, BLEND_TEST_SEEDS_DIR, true),
            'testBlendManyResources() blend attempted'
        );

        // verify that they exist:
        foreach ($resource_seeds as $context => $seeds) {
            foreach ($seeds as $seed) {
                $testResource = $this->modx->getObject('modResource', ['alias' => $seed]);
                $this->assertInstanceOf(
                    '\modResource',
                    $testResource,
                    'Verifying that ' . $seed . ' was created'
                );

                // Resource groups:
                $this->assertEquals(
                    true,
                    $testResource->isMember('Test Resource Group'),
                    'Verifying that ' . $seed . ' was attached to a resource group'
                );

            }
        }
    }

    /**
     * @depends testBlendManyResources
     */
    public function testRevertBlendManyResources()
    {
        $resource_seeds = [
            'web' => [
                'test-blend-many-resource-1',
                'test-blend-many-resource-2'
            ]
        ];

        $this->assertEquals(
            true,
            $this->blender->revertBlendManyResources($resource_seeds, BLEND_TEST_SEEDS_DIR),
            'testBlendManyResources() blend attempted'
        );

        // verify that they exist:
        foreach ($resource_seeds as $context => $seeds ) {
            foreach ($seeds as $seed) {
                $testResource = $this->modx->getObject('modResource', ['alias' => $seed, 'context_key' => $context]);
                $this->assertEquals(
                    false,
                    $testResource,
                    'Verifying that ' . $seed . ' was removed/reverted'
                );
            }
        }
    }

    /**
     * @depends testBlendManyResources
     */
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

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seeds_directory = $this->blender->getMigrationName('resource');

        $seeds = $this->blender->makeResourceSeeds(['alias:IN' => $aliases]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.'testResource2.migration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().'m2018_01_10_093000_Resource.php'))),
            'Comparing existing testResource2 migration file with generated file'
        );

        $count = 3;
        foreach ($test_resources as $alias => $test_resource) {
            $fixed_data = require_once BLEND_COMPARE_DIRECTORY . 'testResource'.$count.'.seed.php';
            $generated_data = false;
            $seed_file = $this->blender->getSeedsPath($seeds_directory) . 'resources' . DIRECTORY_SEPARATOR . 'web'. DIRECTORY_SEPARATOR . $alias.'.cache.php';
            if (file_exists($seed_file)) {
                $generated_data = require_once $seed_file;
            }
            unset($generated_data['columns']['id'], $generated_data['columns']['createdon'], $generated_data['columns']['alias_visible']);

            $this->assertEquals(
                $fixed_data,
                $generated_data,
                'Comparing existing testResource'.$count++.' seed file with generated seed file: '.$seed_file.PHP_EOL
            );
        }

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testMakeResourceSeeds
     */
    public function testCleanUpMakeResourceSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

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
        $this->blender->setSeedsDir($actual_timestamp);
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
