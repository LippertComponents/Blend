<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 10/9/2017
 * Time: 4:07 PM
 */

namespace LCI\Blend;


class Chunk extends Element
{
    /**
     * @return $this
     */
    public function init()
    {
        parent::init();
        $this->setElementClass('modChunk');

        return $this;
    }
}