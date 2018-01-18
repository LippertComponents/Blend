<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/06 at 9:43:39 EST -05:00
 */

use \LCI\Blend\Migrations;

class SnippetMigrationExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \LCI\Blend\Snippet $testSnippet3 */
        $testSnippet3 = $this->blender->blendOneRawSnippet('testSnippet3');
        $testSnippet3
            ->setSeedTimeDir($this->getTimestamp())
            ->setDescription('This is my 3rd test snippet, note this is limited to 255 or something and no HTML')
            ->setCategoryFromNames('Parent Snippet Cat=>Child Snippet Cat')
            ->setCode('<?php return \'This is the 3rd test Snippet!\'; ')
            ;//->setAsStatic('core/components/mysite/elements/snippets/mySnippet3.tpl');

        if ($testSnippet3->blend(true)) {
            $this->blender->out($testSnippet3->getName().' was saved correctly');

        } else {
            //error
            $this->blender->out($testSnippet3->getName().' did not save correctly ', true);
            $this->blender->out(print_r($testSnippet3->getErrorMessages(), true), true);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // set back to previous version of the snippet
        /** @var bool|\modSnippet $testSnippet3 */
        $name = 'testSnippet3';

        $blendSnippet = new \LCI\Blend\Snippet($this->modx, $this->blender);
        $blendSnippet
            ->setName($name)
            ->setSeedTimeDir($this->getTimestamp());

        if ( $blendSnippet->revertBlend() ) {
            $this->blender->out($blendSnippet->getName().' setting has been reverted to '.$this->getTimestamp());

        } else {
            $this->blender->out($blendSnippet->getName().' setting was not reverted', true);
        }

        /**
         * Manually via xPDO, but no control her to what the last version may have been, assuming it did not exist:
        $testSnippet3 = $this->modx->getObject('modSnippet', ['name' => $name]);
        if ($testSnippet3 instanceof \modSnippet) {
            if ($testSnippet3->remove()) {
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
        $this->description = 'This is a test for a simple Snippet migration class';
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
