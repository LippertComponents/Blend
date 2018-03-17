<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 10/9/2017
 * Time: 4:07 PM
 */

namespace LCI\Blend\Blendable;


class Chunk extends Element
{
    /** @var string  */
    protected $opt_cache_key = 'elements/chunks';

    /** @var string ~ the xPDO class name */
    protected $xpdo_simple_object_class = 'modChunk';

    /**
     * @return \LCI\Blend\Blendable\Chunk
     */
    public function getCurrentVersion()
    {
        /** @var \LCI\Blend\Blendable\Chunk $snippet */
        $snippet = new self($this->modx, $this->blender, $this->getFieldName());
        return $snippet
            ->setSeedsDir($this->getSeedsDir());
    }
}
