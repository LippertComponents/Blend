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
        /** @var \LCI\Blend\Chunk $chunk */
        $testChunk3 = $this->blender->blendOneRawChunk('testChunk3');
        $testChunk3
            ->setSeedTimeDir($this->getTimestamp())
            ->setDescription('This is my 3rd test chunk, note this is limited to 255 or something and no HTML')
            ->setCategoryFromNames('Parent Cat=>Child Cat')
            ->setCode('Hi [[+testPlaceholder3]], ...')
            ;//->setAsStatic('core/components/mysite/elements/chunks/myChunk3.tpl');

        if ($testChunk3->blend(true)) {
            $this->blender->out($testChunk3->getName().' was saved correctly');

        } else {
            //error
            $this->blender->out($testChunk3->getName().' did not save correctly ', true);
            $this->blender->out(print_r($testChunk3->getErrorMessages(), true), true);
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

        $blendChunk = new \LCI\Blend\Chunk($this->modx, $this->blender);
        $blendChunk
            ->setName($name)
            ->setSeedTimeDir($this->getTimestamp());

        if ( $blendChunk->revertBlend() ) {
            $this->blender->out($blendChunk->getName().' setting has been reverted to '.$this->getTimestamp());

        } else {
            $this->blender->out($blendChunk->getName().' setting was not reverted', true);
        }

        /**
         * Manually via xPDO, but no control her to what the last version may have been, assuming it did not exist:
        /** @var bool|\modChunk $testChunk3 * /
        $name = 'testChunk3';
        $testChunk3 = $this->modx->getObject('modChunk', ['name' => $name]);
        if ($testChunk3 instanceof \modChunk) {
            if ($testChunk3->remove()) {
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
    protected function assignTimestamp()
    {
        $this->timestamp = BLEND_TEST_TIMESTAMP;
    }
}
