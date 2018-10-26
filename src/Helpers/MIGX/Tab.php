<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/26/18
 * Time: 11:22 AM
 */

namespace LCI\Blend\Helpers\MIGX;


class Tab
{
    /** @var string */
    protected $caption;

    /** @var array ~ [LCI\Blend\Helpers\MIGX\Field, ... ] */
    protected $fields = [];

    /** @var array  */
    protected $custom_properties = [];

    /**
     * Tab constructor.
     * @param string $caption
     */
    public function __construct(string $caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return string
     */
    public function getCaption(): string
    {
        return $this->caption;
    }

    /**
     * @return array ~ [LCI\Blend\Helpers\MIGX\Field, ... ]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $columns ~ any existing columns to pass first
     * @return array
     */
    public function getGridColumns($columns=[])
    {
        $fields = $this->getFields();

        /** @var Field $field */
        foreach ($fields as $field) {
            if ($field->isShowInGrid()) {
                $columns[] = $field->getGridArray();
            }
        }

        return $columns;
    }
    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'caption' => $this->caption,
            'fields' => []
        ];

        /** @var Field $field */
        foreach ($this->fields as $field) {
            $data['fields'][] = $field->toArray();
        }

        return $data;
    }

    /**
     * @param string $name
     * @return Field
     */
    public function makeField(string $name)
    {
        $field = new Field($name);

        $this->fields[] = $field;

        return $field;
    }

    /**
     * Use if a custom MIGX property is needed that is not defined in this object
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setCustomProperty($key, $value): self
    {
        $this->custom_properties[$key] = $value;

        return $this;
    }


}