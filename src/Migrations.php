<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 11/10/2017
 * Time: 5:49 AM
 */

namespace LCI\Blend;

abstract class Migrations
{
    /** @var \modX  */
    protected $modx;

    /** @var  Blender */
    protected $blender;

    /** @var string ~ a description of what this migration will do */
    protected $description = '';

    /** @var string ~ a version number if you choose */
    protected $version = '';

    /** @var string ~ master|staging|dev|local */
    protected $type = 'master';

    /** @var string ~ will be for any seeds to find their related directory */
    protected $timestamp = '';

    /** @var string name of Author of the Migration */
    protected $author = '';

    /**
     * Migrations constructor.
     *
     * @param $modx
     * @param Blender $blender
     */
    public function __construct(\modX &$modx, Blender $blender)
    {
        $this->modx = $modx;
        $this->blender = $blender;

        $this->assignDescription();
        $this->assignVersion();
        $this->assignType();
        $this->assignTimestamp();
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Child class needs to override and implement this
        $this->modx->log(
            modX::LOG_LEVEL_ERROR,
            get_class($this).'::'.__METHOD__.PHP_EOL.
            'Did not implement up()'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Child class needs to override and implement this
        $this->modx->log(
            modX::LOG_LEVEL_ERROR,
            get_class($this).'::'.__METHOD__.PHP_EOL.
            'Did not implement down()'
        );
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return (string)$this->description;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return (string)$this->version;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Method is called on construct, Child class needs to override and implement this
     */
    protected function assignDescription()
    {
        $this->description = '';
    }

    /**
     * Method is called on construct, Child class needs to override and implement this
     */
    protected function assignVersion()
    {
        $this->version = '';
    }

    /**
     * Method is called on construct, Child class can override and implement this
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
        $this->timestamp = '';
    }

}
