<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 3/5/18
 * Time: 11:51 AM
 */

namespace LCI\Blend\Blendable;

use LCI\Blend\Properties;

trait BlendableProperties
{
    /** @var null|Properties  */
    protected $properties = null;

    protected function loadProperties()
    {
        $this->properties = new Properties();
    }

    /**
     * @return Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getPropertiesData()
    {
        return $this->properties->getData();
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties->setProperty($name, $value);
        return $this;
    }
    /**
     * @param array $data
     *
     * @return $this
     */
    public function setProperties(array $data)
    {
        $this->properties->mergePropertiesFromArray($data);
        return $this;
    }

}