<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/18 at 7:48:01 EST -05:00
 */

use \LCI\Blend\Migrations;
use \LCI\Blend\SystemSetting;

class SystemSettingMigrationExample extends Migrations
{
    /** @var array ~ custom settings: */
    protected $settings = [
        [
            'key' => 'testSystemSettingMigration',
            'value' => 'Blend Site',
            'xtype' => 'textfield',
            'namespace' => 'core',
            'area' => 'site',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Existing Core settings:
        $blendExistingSetting = new SystemSetting($this->modx, $this->blender);
        // Can use lots of helper methods for core system settings, see all setCore* methods
        $blendExistingSetting->setCoreSiteName('Blend SystemSettingMigrationExample');
        $blendExistingSetting->setSeedTimeDir($this->getTimestamp());
        $blendExistingSetting->blend();

        // From array:
        $this->blender->blendManySystemSettings($this->settings, $this->getTimestamp());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $my = new SystemSetting($this->modx, $this->blender);
        // Can use lots of helper methods for core system settings, see all setCore* methods
        $my->setCoreSiteName('Blend SystemSettingMigrationExample');
        $my->setSeedTimeDir($this->getTimestamp());
        $my->revertBlend();

        $this->blender->revertBlendManySystemSettings($this->settings, $this->getTimestamp());
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'System settings migration test';
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
        $this->timestamp = BLEND_TEST_TIMESTAMP;
    }
}
