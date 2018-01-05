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
    /** @var string ~ the xPDO class name */
    protected $element_class = 'modSnippet';

    /**
     * @param string $name
     *
     * @return Snippet
     */
    public function loadCurrentVersion($name)
    {
        /** @var Snippet $element */
        $element = new self($this->modx, $this->blender);
        return $element->loadElementFromName($name);
    }
}
