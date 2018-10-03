<?php
//declare(strict_types=1);

final class MediaSourceTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testGetBlendableMediaSource()
    {
        //$this->modx->loadClass('sources.modMediaSource');

        $media_source_name = 'testMediaSource1';
        $ms_description = 'This is my test media source, note this is limited to 255 or something and no HTML';

        /** @var \LCI\Blend\Blendable\MediaSource $testMediaSource1 */
        $testMediaSource1 = $this->blender->getBlendableLoader()->getBlendableMediaSource($media_source_name);
        $testMediaSource1
            ->setSeedsDir($media_source_name)
            ->setFieldDescription($ms_description);

        $blended = $testMediaSource1->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $media_source_name.' media source blend attempted'
        );

        // Validate data:
        if ($blended) {
            /** @var \LCI\Blend\Blendable\MediaSource $blendMediaSource */
            $blendMediaSource = $testMediaSource1->getCurrentVersion();
            $this->assertInstanceOf(
                '\LCI\Blend\Blendable\MediaSource',
                $blendMediaSource,
                'Validate instance was created \LCI\Blend\Blendable\MediaSource'
            );

            if ($blendMediaSource instanceof \LCI\Blend\Blendable\MediaSource) {
                $this->assertEquals(
                    $media_source_name,
                    $blendMediaSource->getFieldName(),
                    'Compare media source name'
                );

                $this->assertEquals(
                    $ms_description,
                    $blendMediaSource->getFieldDescription(),
                    'Compare media source description'
                );
            }
        }
    }

    /**
     * @depends testGetBlendableMediaSource
     */
    public function testMakeMediaSourceSeeds()
    {
        $media_source_name = 'testMediaSource1';

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seeds_directory = $this->blender->getMigrationName('mediaSource');

        $this->blender->getSeedMaker()->makeMediaSourceSeeds(['name' => $media_source_name]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.$media_source_name.'.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().'m2018_01_10_093000_MediaSource.php'))),
            'Comparing existing testMediaSource migration file with generated file'
        );

        $fixed_data = require_once BLEND_COMPARE_DIRECTORY.'testMediaSource1.seed.php';

        $generated_data = false;
        $seed_file = $this->blender->getSeedsPath($seeds_directory).DIRECTORY_SEPARATOR.'media-sources'.DIRECTORY_SEPARATOR.$media_source_name.'.cache.php';
        if (file_exists($seed_file)) {
            $generated_data = require_once $seed_file;
        }

        unset($generated_data['columns']['id'], $generated_data['columns']['properties']['visibility'], $fixed_data['columns']['properties']['visibility']);

        $this->assertEquals(
            $fixed_data,
            $generated_data,
            'Comparing existing testMediaSource seed file with generated seed file: '.PHP_EOL.$seed_file
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testMakeMediaSourceSeeds
     */
    public function testRevertMediSource()
    {
        $media_source_name = 'testMediaSource1';

        /** @var \LCI\Blend\Blendable\MediaSource $testMediaSource1 */
        $blendMediaSource = $this->blender->getBlendableLoader()->getBlendableMediaSource($media_source_name);
        $blendMediaSource
            ->setSeedsDir($media_source_name);

        $this->assertEquals(
            true,
            $blendMediaSource->revertBlend(),
            'Revert blend'
        );
    }
    /**
     * @depends testMakeMediaSourceSeeds
     */
    public function testCleanUpMakeMediaSourceSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $media_source_name = 'testMediaSource2';

        if (BLEND_CLEAN_UP) {
            // Remove created test chunk:
            $testMediaSource = $this->modx->getObject('modMediaSource', ['name' => $media_source_name]);
            if (is_object($testMediaSource)) {
                $testMediaSource->remove();

            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'mediaSource'),
                'Remove created '.$media_source_name.' migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }

    public function testMediaSourceMigration()
    {
        $migration = 'MediaSourceMigrationExample';
        $media_source_name = 'testMediaSource3';
        $description = 'This is my 3rd media source test, note this is limited to 255 or something and no HTML';

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $testMediaSource3 = $this->modx->getObject('modMediaSource', ['name' => $media_source_name]);
        $this->assertInstanceOf(
            '\modMediaSource',
            $testMediaSource3,
            'Validate MediaSourceMigrationExample that media source was created '.$media_source_name
        );

        if ($testMediaSource3 instanceof \modMediaSource) {
            $this->assertEquals(
                $media_source_name,
                $testMediaSource3->get('name'),
                'Compare media source name'
            );

            $this->assertEquals(
                $description,
                $testMediaSource3->get('description'),
                'Compare media source description'
            );

            $data= $testMediaSource3->toArray();
            $this->assertEquals(
                '/assets/path/',
                $data['properties']['basePath']['value'],
                'Compare basePath value code'
            );

            $this->assertEquals(
                '/assets/url/',
                $data['properties']['baseUrl']['value'],
                'Compare baseUrl value code'
            );
        }
    }

    /**
     * @depends testMediaSourceMigration
     */
    public function testMediaSourceRevertMigration()
    {
        $migration = 'MediaSourceMigrationExample';
        $media_source_name = 'testMediaSource3';

        $testMediaSource3 = $this->modx->getObject('modMediaSource', ['name' => $media_source_name]);

        $this->assertInstanceOf(
            '\modMediaSource',
            $testMediaSource3,
            'Validate MediaSourceMigrationExample created '.$media_source_name
        );

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $testMediaSource4 = $this->modx->getObject('modMediaSource', ['name' => $media_source_name]);

        $this->assertEquals(
            false,
            $testMediaSource4,
            'Compare MediaSourceMigrationExample revert, should be empty/false'
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
