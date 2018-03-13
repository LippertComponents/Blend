<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 3/5/18
 * Time: 2:33 PM
 */

namespace LCI\Blend\Blendable;

trait DescriptionGetterAndSetter
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->blendable_xpdo_simple_object_data['description'];
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->blendable_xpdo_simple_object_data['description'] = $description;
        return $this;
    }
}