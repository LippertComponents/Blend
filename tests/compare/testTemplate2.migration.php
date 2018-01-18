<?php

/**
 * Auto Generated from Blender
 * Date: 2018/01/18 at 10:33:54 EST -05:00
 */

use \LCI\Blend\Migrations;

class m2018_01_10_093000_Template extends Migrations
{
    /** @var array  */
    protected $templates = array (
      0 => 'modTemplate_testTemplate2',
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->blender->blendManyTemplates($this->templates, $this->getTimestamp());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->blender->revertBlendManyTemplates($this->templates, $this->getTimestamp());
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
    protected function assignTimestamp()
    {
        $this->timestamp = '2018_01_10_093000';
    }
}