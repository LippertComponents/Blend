<?php

/**
 * Auto Generated from Blender
 * Date: 2018/03/10 at 18:44:07 UTC +00:00
 */

use \LCI\Blend\Migrations;

class MediaSourceMigrationExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \LCI\Blend\Blendable\MediaSource $testMediaSource3 */
        $testMediaSource3 = $this->blender->getBlendableLoader()->getBlendableMediaSource('testMediaSource3');
        $testMediaSource3
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('This is my 3rd media source test, note this is limited to 255 or something and no HTML')
            ->setPropertyBasePath('/assets/path/')
            ->setPropertyBaseUrl('/assets/url/');
        if ($testMediaSource3->blend(true)) {
            $this->blender->out($testMediaSource3->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($testMediaSource3->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($testMediaSource3->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var \LCI\Blend\Blendable\MediaSource $testMediaSource3 */
        $testMediaSource3 = $this->blender->getBlendableLoader()->getBlendableMediaSource('testMediaSource3');

        if ( $testMediaSource3->setSeedsDir($this->getSeedsDir())->revertBlend() ) {
            $this->blender->out($testMediaSource3->getFieldName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($testMediaSource3->getFieldName().' setting was not reverted');
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = '';
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
     * Method is called on construct
     */
    protected function assignSeedsDir()
    {
        $this->seeds_dir = 'MediaSourceMigrationExample';
    }
}
