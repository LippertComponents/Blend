<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LCI\Blend\Blender;
use League\CLImate\CLImate;

final class BlendTest extends BaseBlend
{
    public function testCanBeInstalledBlend()
    {
        $this->blender->install();

        $this->assertEquals(
            true,
            $this->blender->isBlendInstalledInModx()
        );
    }

    public function testCreateBlankMigrationClassFile()
    {
        $migration_class_name = 'BlankMigration';

        $actual_timestamp = $this->blender->getTimestamp();
        $this->blender->setTimestamp(BLEND_TEST_TIMESTAMP);

        $this->assertEquals(
            true,
            $this->blender->createBlankMigrationClassFile($migration_class_name),
            'Create blank migration file'
        );

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.'BlankMigration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationDirectory().'BlankMigration.php'))),
            'Comparing existing blank migration file with generated file'
        );

        $this->blender->setTimestamp($actual_timestamp);
    }

    public function testCleanUpCreateBlankMigrationClassFile()
    {
        $migration_class_name = 'BlankMigration';

        $actual_timestamp = $this->blender->getTimestamp();
        $this->blender->setTimestamp(BLEND_TEST_TIMESTAMP);

        // Clean up
        if (BLEND_CLEAN_UP) {
            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile($migration_class_name, 'blank'),
                'Remove created blank migration file'
            );
        }
        $this->blender->setTimestamp($actual_timestamp);
    }

    public function testCanBeUninstalledBlend()
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
