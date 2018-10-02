<?php
//declare(strict_types=1);

final class ChunkTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testBlendOneRawChunk()
    {
        $chunk_name = 'testChunk1';
        $chunk_description = 'This is my test chunk, note this is limited to 255 or something and no HTML';
        $chunk_code = 'Hi [[+testPlaceholder]]!';
        /** @var \LCI\Blend\Blendable\Chunk $chunk */
        $testChunk1 = $this->blender->getBlendableLoader()->getBlendableChunk($chunk_name);
        $testChunk1
            ->setSeedsDir($chunk_name)
            ->setFieldDescription($chunk_description)
            ->setFieldCategory('Parent Cat=>Child Cat')
            ->setFieldCode($chunk_code, true)
            ->setAsStatic('core/components/mysite/elements/chunks/myChunk.tpl');

        $blended = $testChunk1->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $chunk_name.' chunk blend attempted'
        );

        // Validate data:
        if ($blended) {
            /** @var \LCI\Blend\Blendable\Chunk $blendChunk */
            $blendChunk = $testChunk1->getCurrentVersion();
            $this->assertInstanceOf(
                '\LCI\Blend\Blendable\Chunk',
                $blendChunk,
                'Validate instance was created \LCI\Blend\Chunk'
            );

            if ($blendChunk instanceof \LCI\Blend\Blendable\Chunk) {
                $this->assertEquals(
                    $chunk_name,
                    $blendChunk->getFieldName(),
                    'Compare chunk name'
                );

                $this->assertEquals(
                    $chunk_description,
                    $blendChunk->getFieldDescription(),
                    'Compare chunk description'
                );

                $this->assertEquals(
                    $chunk_code,
                    $blendChunk->getFieldCode(),
                    'Compare chunk code'
                );

                $modChunk = $this->modx->getObject('modChunk', ['name' => $chunk_name]);
                /** @var \modCategory $modCategory */
                $modCategory = $modChunk->getOne('Category');

                $this->assertEquals(
                    'Child Cat',
                    $modCategory->get('category'),
                    'Compare categories'
                );

                $this->assertEquals(
                    true,
                    $blendChunk->revertBlend(),
                    'Revert blend'
                );
            }
        }
    }

    public function testMakeChunkSeeds()
    {
        $chunk_name = 'testChunk2';
        $chunk_description = 'This is my 2nd test chunk, note this is limited to 255 or something and no HTML';
        $chunk_code = 'Hi [[+testPlaceholder2]]!';

        // Make test chunk:
        $testChunk2 = $this->modx->getObject('modChunk', ['name' => $chunk_name]);
        if (is_object($testChunk2)) {
            $this->assertEquals(
                false,
                $testChunk2,
                $chunk_name.' already exists'
            );

        } else {
            $testChunk2 = $this->modx->newObject('modChunk');
            $testChunk2->fromArray([
                'name' => $chunk_name,
                'description' => $chunk_description,
                'content' => $chunk_code
            ]);
            $testChunk2->save();
        }

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seeds_directory = $this->blender->getMigrationName('chunk');

        $this->blender->getSeedMaker()->makeChunkSeeds(['name' => $chunk_name]);

        $this->blender->out('DIR: '.BLEND_COMPARE_DIRECTORY.$chunk_name.'.php', true);
        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.$chunk_name.'.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().'m2018_01_10_093000_Chunk.php'))),
            'Comparing existing testChunk2 migration file with generated file'
        );

        $fixed_data = require_once BLEND_COMPARE_DIRECTORY.'testChunk2.seed.php';
        $generated_data = false;
        $seed_file = $this->blender->getSeedsPath($seeds_directory) . 'elements' . DIRECTORY_SEPARATOR . 'chunks'. DIRECTORY_SEPARATOR .$chunk_name.'.cache.php';
        if (file_exists($seed_file)) {
            $generated_data = require_once $seed_file;
        }
        unset($generated_data['id']);

        $this->assertEquals(
            $fixed_data,
            $generated_data,
            'Comparing existing testChunk2 seed file with generated seed file'
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testMakeChunkSeeds
     */
    public function testCleanUpMakeChunkSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $chunk_name = 'testChunk2';

        if (BLEND_CLEAN_UP) {
            // Remove created test chunk:
            $testChunk2 = $this->modx->getObject('modChunk', ['name' => $chunk_name]);
            if (is_object($testChunk2)) {
                $testChunk2->remove();

            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'chunk'),
                'Remove created chunk2 migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }

    public function testChunkMigration()
    {
        $migration = 'ChunkMigrationExample';
        $chunk_name = 'testChunk3';
        $chunk_description = 'This is my 3rd test chunk, note this is limited to 255 or something and no HTML';
        $chunk_code = 'Hi [[+testPlaceholder3]], ...';

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $testChunk3 = $this->modx->getObject('modChunk', ['name' => $chunk_name]);
        $this->assertInstanceOf(
            '\modChunk',
            $testChunk3,
            'Validate testChunkMigration that chunk was created '.$chunk_name
        );

        if ($testChunk3 instanceof \modChunk) {
            $this->assertEquals(
                $chunk_name,
                $testChunk3->get('name'),
                'Compare chunk name'
            );

            $this->assertEquals(
                $chunk_description,
                $testChunk3->get('description'),
                'Compare chunk description'
            );

            $this->assertEquals(
                $chunk_code,
                $testChunk3->getContent(),
                'Compare chunk code'
            );
        }
    }

    public function testChunkRevertMigration()
    {
        $migration = 'ChunkMigrationExample';
        $chunk_name = 'testChunk3';

        $testChunk3 = $this->modx->getObject('modChunk', ['name' => $chunk_name]);

        $this->assertInstanceOf(
            '\modChunk',
            $testChunk3,
            'Validate testChunkMigration that chunk was created '.$chunk_name
        );

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $testChunk4 = $this->modx->getObject('modChunk', ['name' => $chunk_name]);

        $this->assertEquals(
            false,
            $testChunk4,
            'Compare testChunkRevertMigration, should be empty/false'
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
