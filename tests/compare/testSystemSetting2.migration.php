<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/18 at 7:48:01 EST -05:00
 */

use \LCI\Blend\Migrations;

class m2018_01_10_093000_Systemsettings extends Migrations
{
    /** @var array  */
    protected $settings = array (
      0 => 
      array (
        'key' => 'testSystemSetting2',
        'value' => 'Blend Site',
        'xtype' => 'textfield',
        'namespace' => 'core',
        'area' => 'site',
        'editedon' => '0000-00-00 00:00:00',
      ),
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->blender->blendManySystemSettings($this->settings, $this->getSeedsDir());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->blender->revertBlendManySystemSettings($this->settings, $this->getSeedsDir());
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
        $this->seeds_dir = 'm2018_01_10_093000_Systemsettings';
    }
}
