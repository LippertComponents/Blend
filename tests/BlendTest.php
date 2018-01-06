<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LCI\Blend\Blender;
use League\CLImate\CLImate;
require_once 'BaseBlend.php';

final class BlendTest extends BaseBlend
{
    public function testCanBeInstalledBlend()
    {
        $this->loadDependentClasses();

        $this->blender->install();

        $this->assertEquals(
            true,
            $this->blender->isBlendInstalledInModx()
        );
    }

    public function testBlendOneRawChunk()
    {
        $this->loadDependentClasses();

        $chunk_name = 'testChunk1';
        $chunk_description = 'This is my test chunk, note this is limited to 255 or something and no HTML';
        $chunk_code = 'Hi [[+testPlaceholder]]!';
        /** @var \LCI\Blend\Chunk $chunk */
        $testChunk1 = $this->blender->blendOneRawChunk($chunk_name);
        $testChunk1
            ->setSeedTimeDir($chunk_name)
            ->setDescription($chunk_description)
            ->setCategoryFromNames('Parent Cat=>Child Cat')
            ->setCode($chunk_code)
            ->setAsStatic('core/components/mysite/elements/chunks/myChunk.tpl');

        $blended = $testChunk1->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $chunk_name.' chunk blend attempted'
        );

        // Validate data:
        if ($blended) {
            /** @var \LCI\Blend\Chunk $blendChunk */
            $blendChunk = $testChunk1->loadCurrentVersion($chunk_name);
            $this->assertInstanceOf(
                '\LCI\Blend\Chunk',
                $blendChunk,
                'Validate instance was created \LCI\Blend\Chunk'
            );

            if ($blendChunk instanceof \LCI\Blend\Chunk) {
                $this->assertEquals(
                    $chunk_name,
                    $blendChunk->getName(),
                    'Compare chunk name'
                );

                $this->assertEquals(
                    $chunk_description,
                    $blendChunk->getDescription(),
                    'Compare chunk description'
                );

                $this->assertEquals(
                    $chunk_code,
                    $blendChunk->getCode(),
                    'Compare chunk code'
                );

                $this->assertEquals(
                    true,
                    $blendChunk->revertBlend(),
                    'Revert blend'
                );
            }
        }
    }

}
