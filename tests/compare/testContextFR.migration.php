<?php

/**
 * Auto Generated from Blender
 * Date: 2018/03/26 at 10:41:36 UTC +00:00
 */

use \LCI\Blend\Migrations;

class m2018_01_10_093000_Context extends Migrations
{
    /** @var array  */
    protected $context = array (
      0 => 'fr',
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->blender->blendManyContexts($this->contexts, $this->getSeedsDir());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->blender->revertBlendManyContexts($this->contexts, $this->getSeedsDir());
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
        $this->seeds_dir = 'm2018_01_10_093000_Context';
    }
}
