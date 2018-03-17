<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 10/9/2017
 * Time: 4:06 PM
 */

namespace LCI\Blend\Blendable;


class Snippet extends Element
{

    /** @var string  */
    protected $opt_cache_key = 'elements/snippets';

    /** @var string ~ the xPDO class name */
    protected $xpdo_simple_object_class = 'modSnippet';

    /**
     * @return \LCI\Blend\Blendable\Snippet
     */
    public function getCurrentVersion()
    {
        /** @var \LCI\Blend\Blendable\Snippet $snippet */
        $snippet = new self($this->modx, $this->blender, $this->getFieldName());
        return $snippet
            ->setSeedsDir($this->getSeedsDir());
    }
}
