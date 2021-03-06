<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/06 at 9:43:39 EST -05:00
 */

use \LCI\Blend\Migrations;

class TemplateMigrationExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \LCI\Blend\Blendable\Template $testTemplate3 */
        $testTemplate3 = $this->blender->getBlendableLoader()->getBlendableTemplate('testTemplate3');
        $testTemplate3
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('This is my 3rd test template, note this is limited to 255 or something and no HTML')
            ->setFieldCategory('Parent Template Cat=>Child Template Cat')
            ->setFieldCode('<!DOCTYPE html><html lang="en"><head><title>[[*pagetitle]]</title></head><body><!-- 3rd -->[[*content]]</body></html>')
            ;//->setAsStatic('core/components/mysite/elements/templates/myTemplate3.tpl');

        if ($testTemplate3->blend(true)) {
            $this->blender->out($testTemplate3->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($testTemplate3->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($testTemplate3->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // set back to previous version of the template
        $name = 'testTemplate3';

        $blendTemplate = $this->blender->getBlendableLoader()->getBlendableTemplate($name);
        $blendTemplate->setSeedsDir($this->getSeedsDir());

        if ( $blendTemplate->revertBlend() ) {
            $this->blender->out($blendTemplate->getFieldName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($blendTemplate->getFieldName().' setting was not reverted');
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'This is a test for a simple Template migration class';
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
