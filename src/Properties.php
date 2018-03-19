<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 11:50 AM
 */

namespace LCI\Blend;

class Properties
{

    /** @var array  */
    protected $data = [];

    /**
     * Properties constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function mergePropertiesFromArray(array $data=[])
    {
        if ($this->verifyArray($data)) {
            $this->data = array_merge($data, $this->data);
        } else {
            // @TODO something
        }
        return $this;
    }

    protected function verifyArray($data)
    {
        // @TODO
        return true;
    }

}