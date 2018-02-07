<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/17 at 15:41:06 EST -05:00
 */

use \LCI\Blend\Migrations;

class m2018_01_10_093000_Plugin extends Migrations
{
    /** @var array  */
    protected $plugins = array (
      0 => 'modPlugin_testPlugin2',
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->blender->blendManyPlugins($this->plugins, $this->getSeedsDir());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->blender->revertBlendManyPlugins($this->plugins, $this->getSeedsDir());
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
     * Method is called on construct, Child class can override and implement this
     */
    protected function assignSeedsDir()
    {
        $this->seeds_dir = '2018_01_10_093000';
    }
}
