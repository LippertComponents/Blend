<?php
//declare(strict_types=1);

final class BlendTest extends BaseBlend
{
    public function testCanBeInstalledBlend()
    {
        $this->blender->install();

        $this->assertEquals(
            true,
            $this->blender->isBlendInstalledInModx(),
            'Blend did not install'
        );
    }

    /**
     * @depends testCanBeInstalledBlend
     */
    public function testCreateBlankMigrationClassFile()
    {
        $migration_class_name = 'BlankMigration';

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $this->assertEquals(
            true,
            $this->blender->createBlankMigrationClassFile($migration_class_name),
            'Create blank migration file'
        );

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.$migration_class_name.'.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().$this->blender->getMigrationName('blank','BlankMigration').'.php'))),
            'Comparing existing blank migration file with generated file'
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testCreateBlankMigrationClassFile
     */
    public function testCleanUpCreateBlankMigrationClassFile()
    {
        $migration_class_name = 'BlankMigration';

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        // Clean up
        if (BLEND_CLEAN_UP) {
            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile($migration_class_name, 'blank'),
                'Remove created blank migration file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testCanBeInstalledBlend
     */
    public function testCanBeUninstalledBlend()
    {
        if (BLEND_CLEAN_UP) {
            $this->blender->uninstall();

            $this->assertEquals(
                false,
                $this->blender->isBlendInstalledInModx()
            );
        }
    }
}
