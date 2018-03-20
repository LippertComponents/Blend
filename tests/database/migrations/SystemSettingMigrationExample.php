<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/18 at 7:48:01 EST -05:00
 */

use \LCI\Blend\Migrations;
use \LCI\Blend\Blendable\SystemSetting;

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
        /** @var \LCI\Blend\Blendable\SystemSetting $blendExistingSetting */
        $blendExistingSetting = $this->blender->getBlendableSystemSetting();
        // Can use lots of helper methods for core system settings, see all setCore* methods
        $blendExistingSetting
            ->setSeedsDir($this->getSeedsDir())
            ->setCoreSiteName('Blend SystemSettingMigrationExample')
            ->blend();

        // From array:
        $this->blender->blendManySystemSettings($this->settings, $this->getSeedsDir());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var \LCI\Blend\Blendable\SystemSetting $blendExistingSetting */
        $my = $this->blender->getBlendableSystemSetting();
        // Can use lots of helper methods for core system settings, see all setCore* methods
        $my
            ->setSeedsDir($this->getSeedsDir())
            ->setCoreSiteName('Blend SystemSettingMigrationExample')
            ->revertBlend();

        $this->blender->revertBlendManySystemSettings($this->settings, $this->getSeedsDir());
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
    protected function assignSeedsDir()
    {
        $this->seeds_dir = BLEND_TEST_SEEDS_DIR;
    }
}
