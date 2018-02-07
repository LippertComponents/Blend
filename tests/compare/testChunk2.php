<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/06 at 19:35:14 EST -05:00
 */

use \LCI\Blend\Migrations;

class m2018_01_10_093000_Chunk extends Migrations
{
    /** @var array  */
    protected $chunks = array (
      0 => 'modChunk_testChunk2',
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->blender->blendManyChunks($this->chunks, $this->getSeedsDir());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->blender->revertBlendManyChunks($this->chunks, $this->getSeedsDir());
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
        $this->seeds_dir = 'm2018_01_10_093000_Chunk';
    }
}
