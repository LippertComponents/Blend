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
            ->setSeedsDir($context_key)
            ->setFieldDescription($description)
            ->setFieldName('Spanish')
            ->setFieldRank(2)
            ->addSetting('my.setting', '2')
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
                            2,
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
