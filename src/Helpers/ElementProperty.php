<?php

namespace LCI\Blend\Helpers;


class ElementProperty
{
    /** @var string  */
    protected $area = '';

    /** @var string  */
    protected $description = ''; // description or desc??

    /** @var null|string */
    protected $lexicon = null;

    /** @var string */
    protected $name;

    /** @var array ~ only for color and list */
    protected $options = [];

    /** @var string ~ combo-boolean, textfield, textarea, datefield, list, , numberfield, file, color */
    protected $type = 'textfield'; // xtype, type

    /** @var mixed */
    protected $value;

    /**
     * ElementProperty constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'desc' => $this->description,
            'type' => $this->type,
            'options' => $this->options,
            'value' => $this->value,
            'lexicon' => $this->lexicon,
            'area' => $this->area
        ];
    }

    /**
     * @param string $area
     * @return ElementProperty
     */
    public function setArea(string $area): ElementProperty
    {
        $this->area = $area;
        return $this;
    }

    /**
     * @param string $description
     * @return ElementProperty
     */
    public function setDescription(string $description): ElementProperty
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param null|string $lexicon
     * @return ElementProperty
     */
    public function setLexicon(string $lexicon): ElementProperty
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * @param string $text
     * @param $value
     * @return $this
     */
    public function addOption(string $text, $value)
    {
        $this->options[] = [
            'text' => $text,
            'value' => $value
        ];

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        foreach ($options as $option) {
            $this->options[] = $option;
        }

        return $this;
    }

    /**
     * @param array $options ~ [['text' => 'Option', 'value' => 'opt'], ... ]
     *  only valid for color and list
     * @return ElementProperty
     */
    public function setOptions(array $options): ElementProperty
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $type
     *  combo-boolean, textfield, textarea, datefield, list, , numberfield, file, color
     * @return ElementProperty
     */
    public function setType(string $type): ElementProperty
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param mixed $value
     * @return ElementProperty
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}