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
    /** @var string ~ the xPDO class name */
    protected $element_class = 'modChunk';

    /**
     * @param string $name
     *
     * @return Chunk
     */
    public function loadCurrentVersion($name)
    {
        /** @var Chunk $element */
        $element = new self($this->modx, $this->blender);
        $element->setSeedsDir($this->getSeedsDir());
        return $element->loadElementFromName($name);
    }
}
