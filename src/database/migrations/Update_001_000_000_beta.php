<?php

/**
 * Auto Generated from Blender
 * Date: 2017/11/10 at 15:29:37 EST -05:00
 */

use \LCI\Blend\Migrations;

class Update_001_000_000_beta extends Migrations
{

    protected $empty_settings = [
        'blend.portable.systemSettings.templates',
        'blend.portable.systemSettings.mediaSources',
        'blend.portable.systemSettings.resources',

        'blend.portable.templateVariables.templates',
        'blend.portable.templateVariables.mediaSources',
        'blend.portable.templateVariables.resources',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // only thing to change is the version number

        /** @var \LCI\Blend\Blendable\SystemSetting $systemSetting */
        $systemSetting = $this->blender->getBlendableLoader()->getBlendableSystemSetting('blend.version');
        $systemSetting
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldValue('1.0.0 beta')
            ->setFieldArea('Blend')
            ->blend(true);

        foreach ($this->empty_settings as $key) {

            /** @var \LCI\Blend\Blendable\SystemSetting $systemSetting */
            $systemSetting = $this->blender->getBlendableLoader()->getBlendableSystemSetting($key);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->setFieldArea('Blend')
                ->blend(true);
        }

        $this->modx->cacheManager->refresh();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var \LCI\Blend\Blendable\SystemSetting $systemSetting */
        $systemSetting = $this->blender->getBlendableLoader()->getBlendableSystemSetting('blend.version');
        $systemSetting
            ->setSeedsDir($this->getSeedsDir())
            ->revertBlend();

        foreach ($this->empty_settings as $key) {

            /** @var \LCI\Blend\Blendable\SystemSetting $systemSetting */
            $systemSetting = $this->blender->getBlendableLoader()->getBlendableSystemSetting($key);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->revertBlend();
        }

        $this->modx->cacheManager->refresh();
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'Update Blend to v1.0.0 beta from v0.9.11 and below';
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignVersion()
    {
        $this->version = '1.0.0 beta';
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
        $this->seeds_dir = 'Update_001_000_000_beta';
    }
}
