<?php

/**
 * Auto Generated from Blender
 * Date: 2017/11/10 at 15:29:37 EST -05:00
 */

use \LCI\Blend\Migrations;

class v0_9_7_update extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \xPDOManager $manager */
        $manager = $this->modx->getManager();

        // the class table object name
        if ($manager->addField('BlendMigrations', 'author', ['after' => 'status'])) {
            $this->blender->outSuccess('The author column was add to the BlendMigrations class ('.
                $this->modx->getTableName('BlendMigrations').') successfully');

        } else {
            $this->blender->out('The author column was not added to the BlendMigrations class ('.
                $this->modx->getTableName('BlendMigrations').') successfully', true);
        }

        /** @var \LCI\Blend\SystemSetting $systemSetting */
        $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
        $systemSetting
            ->setName('blend.version')
            ->setSeedsDir($this->getSeedsDir())
            ->setValue('0.9.7')
            ->setArea('Blend')
            ->blend();

        $this->modx->cacheManager->refresh();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var \LCI\Blend\SystemSetting $systemSetting */
        $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
        $systemSetting
            ->setName('blend.version')
            ->setSeedsDir($this->getSeedsDir())
            ->revertBlend();

        $this->modx->cacheManager->refresh();

        $previous_version = $this->modx->getOption('blend.version');
        if (!$previous_version || version_compare($this->getVersion(), $previous_version, '>') ) {

            /** @var \xPDOManager $manager */
            $manager = $this->modx->getManager();

            // the class table object name
            if ($manager->removeField('BlendMigrations', 'author', ['after' => 'status'])) {
                $this->blender->outSuccess('The author column was removed from the BlendMigrations class ('.
                    $this->modx->getTableName('BlendMigrations').') successfully');

            } else {
                $this->blender->out('The author column was not removed from the BlendMigrations class ('.
                    $this->modx->getTableName('BlendMigrations').') successfully', true);
            }
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'Update Blend to v0.9.7 from v0.9.6 and below';
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignVersion()
    {
        $this->version = '0.9.7';
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
        $this->seeds_dir = '2018_02_07_070707';
    }
}
