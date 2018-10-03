<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/06 at 9:43:39 EST -05:00
 */

use \LCI\Blend\Migrations;

class ContextMigrationExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \LCI\Blend\Blendable\Context $testContext3 */
        $testContext3 = $this->blender->getBlendableLoader()->getBlendableContext('it');
        $testContext3
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldName('Italian')
            ->setFieldDescription('Italian language')
            ->addSetting('cultureKey', 'it')
            ->addSetting('http_host', 'mysite.com')
            ->addSetting('base_url', '/it/')
            ->addSetting('site_url', 'https://mysite.com/it/');

        if ($testContext3->blend(true)) {
            $this->blender->out($testContext3->getFieldKey().' was saved correctly');

        } else {
            //error
            $this->blender->outError($testContext3->getFieldKey().' did not save correctly ');
            $this->blender->outError(print_r($testContext3->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $context_key = 'it';

        $blendContext = $this->blender->getBlendableLoader()->getBlendableContext($context_key);
        $blendContext->setSeedsDir($this->getSeedsDir());

        if ( $blendContext->revertBlend() ) {
            $this->blender->out($blendContext->getFieldKey().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($blendContext->getFieldKey().' setting was not reverted');
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'This is a test for a simple Context migration class example';
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
