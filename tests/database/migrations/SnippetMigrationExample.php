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
        /** @var \LCI\Blend\Blendable\Snippet $testSnippet3 */
        $testSnippet3 = $this->blender->getBlendableLoader()->getBlendableSnippet('testSnippet3');
        $testSnippet3
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('This is my 3rd test snippet, note this is limited to 255 or something and no HTML')
            ->setFieldCategory('Parent Snippet Cat=>Child Snippet Cat')
            ->setFieldCode('<?php return \'This is the 3rd test Snippet!\'; ')
            ;//->setAsStatic('core/components/mysite/elements/snippets/mySnippet3.tpl');

        if ($testSnippet3->blend(true)) {
            $this->blender->out($testSnippet3->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->out($testSnippet3->getFieldName().' did not save correctly ', true);
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
        $name = 'testSnippet3';

        /** @var \LCI\Blend\Blendable\Snippet $blendSnippet */
        $blendSnippet = $this->blender->getBlendableLoader()->getBlendableSnippet($name);
        $blendSnippet->setSeedsDir($this->getSeedsDir());

        if ( $blendSnippet->revertBlend() ) {
            $this->blender->out($blendSnippet->getFieldName().' snippet has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->out($blendSnippet->getFieldName().' snippet was not reverted', true);
        }
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
    protected function assignSeedsDir()
    {
        $this->seeds_dir = BLEND_TEST_SEEDS_DIR;
    }
}
