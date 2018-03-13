<?php

/**
 * Auto Generated from Blender
 * Date: 2018/03/10 at 20:06:52 UTC +00:00
 */

use \LCI\Blend\Migrations;

class m2018_01_10_093000_MediaSource extends Migrations
{
    /** @var array  */
    protected $media_sources = array (
      0 => 'testMediaSource1',
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->blender->blendManyMediaSources($this->media_sources, $this->getSeedsDir());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->blender->revertBlendManyMediaSources($this->media_sources, $this->getSeedsDir());
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
        $this->seeds_dir = 'm2018_01_10_093000_MediaSource';
    }
}
