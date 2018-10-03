<?php

/**
 * Auto Generated from Blender
 * Date: 2018/02/09 at 17:57:10 UTC +00:00
 */

use \LCI\Blend\Migrations;

class TVsLoadFromSeedsExample extends Migrations
{
    /** @var array  */
    protected $templates = array (
      0 => 'TVAllTestTypes',
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->blender->getBlendableLoader()->blendManyTemplates($this->templates, $this->getSeedsDir(), true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->blender->getBlendableLoader()->revertBlendManyTemplates($this->templates, $this->getSeedsDir());
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'Load Template with all default TV types';
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