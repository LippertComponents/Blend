<?php

use \LCI\Blend\Migrations;

class TemplateVariableMigrationExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \LCI\Blend\Blendable\TemplateVariable $tvTextExample */
        $tvTextExample = $this->blender->getBlendableLoader()->getBlendableTemplateVariable('tvTextExample');
        $tvTextExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('This is text TV, note this is limited to 255 or something and no HTML')
            ->setFieldCategory('Parent Cat=>Child Cat')
            ->setFieldCaption('This is the caption')
            ->setFieldType('text')
            ->setFieldInputProperties(
                $tvTextExample->getInputPropertyHelper()
                ->setTextMinLength(10)
                ->setTextMaxLength(255)
                ->getInputProperties()
            );

        if ($tvTextExample->blend(true)) {
            $this->blender->out($tvTextExample->getFieldName().' TV was saved correctly');

        } else {
            //error
            $this->blender->outError($tvTextExample->getFieldName().' TV did not save correctly ');
            $this->blender->outError(print_r($tvTextExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }

        /** @var \LCI\Blend\Blendable\TemplateVariable $tvNumberExample */
        $tvNumberExample = $this->blender->getBlendableLoader()->getBlendableTemplateVariable('tvNumberExample');
        $tvNumberExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('This is number TV, note this is limited to 255 or something and no HTML')
            ->setFieldCategory('Parent Cat=>Child Cat')
            ->setFieldCaption('Make it a number')
            ->setFieldType('number')
            ->setFieldInputProperties(
                $tvNumberExample->getInputPropertyHelper()
                    ->setNumberMinValue(10)
                    ->setNumberMaxvalue(255)
                    ->setNumberAllowNegative(false)
                    ->setNumberAllowDecimals(false)
                    ->setNumberDecimalPrecision(0)
                    ->setNumberDecimalSeparator(',')
                    ->getInputProperties()
            );

        if ($tvNumberExample->blend(true)) {
            $this->blender->out($tvNumberExample->getFieldName().' TV was saved correctly');

        } else {
            //error
            $this->blender->outError($tvNumberExample->getFieldName().' TV did not save correctly ');
            $this->blender->outError(print_r($tvNumberExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var \LCI\Blend\Blendable\TemplateVariable $tvTextExample */
        $tvTextExample = $this->blender->getBlendableLoader()->getBlendableTemplateVariable('tvTextExample');
        $tvTextExample->setSeedsDir($this->getSeedsDir());

        if ( $tvTextExample->revertBlend() ) {
            $this->blender->out($tvTextExample->getFieldName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($tvTextExample->getFieldName().' setting was not reverted');
        }

        /** @var \LCI\Blend\Blendable\TemplateVariable $tvNumberExample */
        $tvNumberExample = $this->blender->getBlendableLoader()->getBlendableTemplateVariable('tvNumberExample');
        $tvNumberExample->setSeedsDir($this->getSeedsDir());

        if ( $tvNumberExample->revertBlend() ) {
            $this->blender->out($tvNumberExample->getFieldName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($tvNumberExample->getFieldName().' setting was not reverted');
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'This is a test for a Template Variable migration class';
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignVersion()
    {

    }

    /**
     * Method is called on construct, can change to only run this migration for those types
     */
    protected function assignType()
    {
        $this->type = 'master';
    }

    /**
     * Method is called on construct, Child class can override and implement this
     */
    protected function assignSeedsDir()
    {
        $this->seeds_dir = BLEND_TEST_SEEDS_DIR;
    }
}
