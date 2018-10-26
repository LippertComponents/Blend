<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/26/18
 * Time: 11:56 AM
 */

namespace LCI\Blend\Helpers\TVInput;


class OptionValues
{
    protected $options = [];

    protected $separator = '||';

    protected $value_separator = '==';

    /**
     * OptionValues constructor.
     * @param string $separator ~ the separator between items
     * @param string $value_separator ~ the separator between label and value
     */
    public function __construct(string $separator='||', string $value_separator='==')
    {
        $this->separator = $separator;
        $this->value_separator = $value_separator;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $string = '';
        foreach ($this->options as $label => $value) {
            if (!empty($string)) {
                $string .= $this->separator;
            }
            $string .= $label . $this->value_separator . $value;
        }

        return $string;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $label
     * @param null $value
     * @return $this
     */
    public function setOption(string $label, $value=null): self
    {
        if (is_null($value)) {
            $value = $label;
        }
        $this->options[$label] = $value;
        return $this;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link https://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->toString();
    }
}