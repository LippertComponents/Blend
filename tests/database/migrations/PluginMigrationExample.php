<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/06 at 9:43:39 EST -05:00
 */

use \LCI\Blend\Migrations;

class PluginMigrationExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $plugin_event = 'OnWebPageInit';

        /** @var \LCI\Blend\Plugin $testPlugin3 */
        $testPlugin3 = $this->blender->blendOneRawPlugin('testPlugin3');
        $testPlugin3
            ->setSeedsDir($this->getSeedsDir())
            ->setDescription('This is my 3rd test plugin, note this is limited to 255 or something and no HTML')
            ->setCategoryFromNames('Parent Plugin Cat=>Child Plugin Cat')
            ->setCode('<?php $eventName = $modx->event->name;//3rd ')
            ->attachOnEvent($plugin_event)
            ;//->setAsStatic('core/components/mysite/elements/plugins/myPlugin3.tpl');

        if ($testPlugin3->blend(true)) {
            $this->blender->out($testPlugin3->getName().' was saved correctly');

        } else {
            //error
            $this->blender->out($testPlugin3->getName().' did not save correctly ', true);
            $this->blender->out(print_r($testPlugin3->getErrorMessages(), true), true);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // set back to previous version of the plugin
        $name = 'testPlugin3';

        $blendPlugin = new \LCI\Blend\Plugin($this->modx, $this->blender);
        $blendPlugin
            ->setName($name)
            ->setSeedsDir($this->getSeedsDir());

        if ( $blendPlugin->revertBlend() ) {
            $this->blender->out($blendPlugin->getName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->out($blendPlugin->getName().' setting was not reverted', true);
        }

        /**
         * Manually via xPDO, but no control her to what the last version may have been, assuming it did not exist:
        /** @var bool|\modPlugin $testPlugin3 * /
        $testPlugin3 = $this->modx->getObject('modPlugin', ['name' => $name]);
        if ($testPlugin3 instanceof \modPlugin) {
            if ($testPlugin3->remove()) {
                $this->blender->out($name.' has been removed');
            } else {
                $this->blender->out($name.' could not be removed', true);
            }
        }
         */
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'This is a test for a simple Plugin migration class';
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
