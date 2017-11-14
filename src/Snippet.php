<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 10/9/2017
 * Time: 4:06 PM
 */

namespace LCI\Blend;


class Snippet extends Element
{
    /**
     * @return $this
     */
    public function init()
    {
        parent::init();
        $this->setElementClass('modSnippet');

        return $this;
    }
}