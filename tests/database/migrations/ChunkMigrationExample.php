<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/06 at 9:43:39 EST -05:00
 */

use \LCI\Blend\Migrations;

class ChunkMigrationExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \LCI\Blend\Blendable\Chunk $chunk */
        $testChunk3 = $this->blender->getBlendableLoader()->getBlendableChunk('testChunk3');
        $testChunk3
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('This is my 3rd test chunk, note this is limited to 255 or something and no HTML')
            ->setFieldCategory('Parent Cat=>Child Cat')
            ->setFieldCode('Hi [[+testPlaceholder3]], ...')
            ->setProperty('someProperty', '123', 'Description goes here', 'number')
            ->setElementProperty(
                (new \LCI\Blend\Helpers\ElementProperty('2ndProperty'))
                ->setValue(321)
                ->setDescription('This allow you to define object better')
                ->setType('list')
                ->addOption('Option 1', 1)
                ->addOption('Option 2', 2)
                ->setArea('Lists')
                ->setLexicon('tests')
            )
        ;

        if ($testChunk3->blend(true)) {
            $this->blender->out($testChunk3->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($testChunk3->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($testChunk3->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $name = 'testChunk3';

        $blendChunk = $this->blender->getBlendableLoader()->getBlendableChunk($name);
        $blendChunk->setSeedsDir($this->getSeedsDir());

        if ( $blendChunk->revertBlend() ) {
            $this->blender->out($blendChunk->getFieldName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($blendChunk->getFieldName().' setting was not reverted');
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'This is a test for a simple Chunk migration class';
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
