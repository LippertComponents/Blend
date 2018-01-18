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
        /** @var \LCI\Blend\Template $testTemplate3 */
        $testTemplate3 = $this->blender->blendOneRawTemplate('testTemplate3');
        $testTemplate3
            ->setDescription('This is my 3rd test template, note this is limited to 255 or something and no HTML')
            ->setCategoryFromNames('Parent Template Cat=>Child Template Cat')
            ->setCode('<!DOCTYPE html><html lang="en"><head><title>[[*pagetitle]]</title></head><body><!-- 3rd -->[[*content]]</body></html>')
            ;//->setAsStatic('core/components/mysite/elements/templates/myTemplate3.tpl');

        if ($testTemplate3->blend(true)) {
            $this->blender->out($testTemplate3->getName().' was saved correctly');

        } else {
            //error
            $this->blender->out($testTemplate3->getName().' did not save correctly ', true);
            $this->blender->out(print_r($testTemplate3->getErrorMessages(), true), true);
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
        /** @var bool|\modTemplate $testTemplate3 */
        $name = 'testTemplate3';

        $blendTemplate = new \LCI\Blend\Template($this->modx, $this->blender);
        $blendTemplate
            ->setName($name)
            ->setSeedTimeDir($this->getTimestamp());

        if ( $blendTemplate->revertBlend() ) {
            $this->blender->out($blendTemplate->getName().' setting has been reverted to '.$this->getTimestamp());

        } else {
            $this->blender->out($blendTemplate->getName().' setting was not reverted', true);
        }

        /**
         * Manually via xPDO, but no control here to what the last version may have been, assuming it did not exist:
        $testTemplate3 = $this->modx->getObject('modTemplate', ['name' => $name]);
        if ($testTemplate3 instanceof \modTemplate) {
            if ($testTemplate3->remove()) {
                $this->blender->out($name.' has been removed');
            } else {
                $this->blender->out($name.' could not be removed', true);
            }
        }
         */
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
    protected function assignTimestamp()
    {
        $this->timestamp = '2018_01_18_094339';
    }
}
