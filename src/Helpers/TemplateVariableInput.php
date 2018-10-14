<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/4/18
 * Time: 12:30 PM
 */

namespace LCI\Blend\Helpers;

/**
 * Class TemplateVariableInput
 * Simple helper to help set up a proper input for a TV
 * @package LCI\Blend\Helpers
 */
class TemplateVariableInput
{
    /** @var string  */
    protected $type = '';

    /** @var array  */
    protected $input_properties = [];

    /**
     * TemplateVariableInput constructor.
     * @param string $type ~ default options:
     *      autotag, checkbox, date, listbox, listbox-multiple, email, file,
     *      hidden, image, number, option [radio], resourcelist, richtext, tag, text, textarea, url
     * @see https://docs.modx.com/revolution/2.x/making-sites-with-modx/customizing-content/template-variables/template-variable-input-types
     * See manager/templates/default/element/tv/renders/input/ files for related code
     */
    public function __construct(string $type)
    {
        $this->type = $type;
        switch ($this->type) {
            case 'autotag':
                /** @var string ~ autoTag comma separated list of ints */
                $this->input_properties['parent_resources'] = '';
                break;

            case 'checkbox':
                // no break
            case 'option':// radio
                // checkbox, Date, listboxMulti, listboxSingle, radio
                /** @var int  */
                $this->input_properties['columns'] = 1;

                break;

            case 'date':
                $this->input_properties = $this->getDefaultDateProperties();
                break;

            case 'listbox':
                $this->input_properties = $this->getDefaultListboxProperties();
                break;

            case 'listbox-multiple':
                $this->input_properties = $this->getDefaultListboxProperties();
                /** @var bool ~ vertical or horizontal */
                $this->input_properties['stackItems'] = false;
                break;

            case 'number':
                $this->input_properties = $this->getDefaultNumberProperties();
                break;

            case 'resourcelist':
                $this->input_properties = $this->getDefaultResourceListProperties();
                break;

            case 'email':
                // no break
            case 'file':
                // no break
            case 'hidden':
                // no break
            case 'image':
                // no break
            case 'text':
                // no break
            case 'richtext':
                // no break
            case 'tag':
                // no break
            case 'textarea':
                // no break
            case 'url':
                // no break
            default:
                $this->input_properties = $this->getDefaultTextProperties();
                break;

        }

        $this->input_properties['allowBlank'] = true;

    }

    public function getInputProperties()
    {
        return $this->input_properties;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return TemplateVariableInput
     */
    public function setCustomInputProperty($key, $value): self
    {
        $this->input_properties[$key] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowBlank(): bool
    {
        return $this->input_properties['allowBlank'];
    }

    /**
     * @param bool $allowBlank
     * @return TemplateVariableInput
     */
    public function setAllowBlank(bool $allowBlank): self
    {
        $this->input_properties['allowBlank'] = $allowBlank;
        return $this;
    }

    /**
     * @param int $columns
     * Used for type: checkbox, date, listboxMulti, listboxSingle, radio
     * @return TemplateVariableInput
     */
    public function setColumns(int $columns): self
    {
        $this->input_properties['columns'] = $columns;
        return $this;
    }

    /**
     * @param string $parent_resources ~ for autotag
     * @return TemplateVariableInput
     */
    public function setParentResources(string $parent_resources): self
    {
        $this->input_properties['parent_resources'] = $parent_resources;
        return $this;
    }

    /**
     * @param string $disabledDates
     * @return TemplateVariableInput
     */
    public function setDateDisabledDates(string $disabledDates): self
    {
        $this->input_properties['disabledDates'] = $disabledDates;
        return $this;
    }

    /**
     * @param string $disabledDays
     * @return TemplateVariableInput
     */
    public function setDateDisabledDays(string $disabledDays): self
    {
        $this->input_properties['disabledDays'] = $disabledDays;
        return $this;
    }

    /**
     * @param string $minDateValue
     * @return TemplateVariableInput
     */
    public function setDateMinDateValue(string $minDateValue): self
    {
        $this->input_properties['minDateValue'] = $minDateValue;
        return $this;
    }

    /**
     * @param string $minTimeValue
     * @return TemplateVariableInput
     */
    public function setDateMinTimeValue(string $minTimeValue): self
    {
        $this->input_properties['minTimeValue'] = $minTimeValue;
        return $this;
    }

    /**
     * @param string $maxDateValue
     * @return TemplateVariableInput
     */
    public function setDateMaxDateValue(string $maxDateValue): self
    {
        $this->input_properties['maxDateValue'] = $maxDateValue;
        return $this;
    }

    /**
     * @param string $maxTimeValue
     * @return TemplateVariableInput
     */
    public function setDateMaxTimeValue(string $maxTimeValue): self
    {
        $this->input_properties['maxTimeValue'] = $maxTimeValue;
        return $this;
    }

    /**
     * @param string $startDay
     * @return TemplateVariableInput
     */
    public function setDateStartDay(string $startDay): self
    {
        $this->input_properties['startDay'] = $startDay;
        return $this;
    }

    /**
     * @param string $timeIncrement
     * @return TemplateVariableInput
     */
    public function setDateTimeIncrement(string $timeIncrement): self
    {
        $this->input_properties['timeIncrement'] = $timeIncrement;
        return $this;
    }

    /**
     * @param bool $hideTime
     * @return TemplateVariableInput
     */
    public function setDateHideTime(bool $hideTime): self
    {
        $this->input_properties['hideTime'] = $hideTime;
        return $this;
    }


    /**
     * @param string $minLength
     * For types: email, file, hidden, image, text, richtext, tag, textarea, url
     * @return TemplateVariableInput
     */
    public function setTextMinLength(string $minLength): self
    {
        $this->input_properties['minLength'] = $minLength;
        return $this;
    }

    /**
     * @param string $maxLength
     * For types: email, file, hidden, image, text, richtext, tag, textarea, url
     * @return TemplateVariableInput
     */
    public function setTextMaxLength(string $maxLength): self
    {
        $this->input_properties['maxLength'] = $maxLength;
        return $this;
    }

    /**
     * @param string $regex ~ Regular Expression Validator
     * @see https://regex101.com/#javascript
     * For types: email, file, hidden, image, text, richtext, tag, textarea, url
     * @return TemplateVariableInput
     */
    public function setTextRegex(string $regex): self
    {
        $this->input_properties['regex'] = $regex;
        return $this;
    }

    /**
     * @param string $regexText ~ Regular Expression Error
     * For types: email, file, hidden, image, text, richtext, tag, textarea, url
     * @return TemplateVariableInput
     */
    public function setTextRegexText(string $regexText): self
    {
        $this->input_properties['regexText'] = $regexText;
        return $this;
    }


    /**
     * @param string $listWidth
     * For types: listbox & listbox-multiple
     * @return TemplateVariableInput
     */
    public function setListBoxWidth(string $listWidth): self
    {
        $this->input_properties['listWidth'] = $listWidth;
        return $this;
    }

    /**
     * @param string $title
     * For types: listbox & listbox-multiple
     * @return TemplateVariableInput
     */
    public function setListBoxTitle(string $title): self
    {
        $this->input_properties['title'] = $title;
        return $this;
    }

    /**
     * @param string $listEmptyText
     * For types: listbox & listbox-multiple
     * @return TemplateVariableInput
     */
    public function setListBoxEmptyText(string $listEmptyText): self
    {
        $this->input_properties['listEmptyText'] = $listEmptyText;
        return $this;
    }

    /**
     * @param bool $stackItems
     * For types: listbox & listbox-multiple
     * @return TemplateVariableInput
     */
    public function setListBoxStackItems(bool $stackItems): self
    {
        $this->input_properties['stackItems'] = $stackItems;
        return $this;
    }

    /**
     * @param bool $typeAhead
     * For types: listbox & listbox-multiple
     * @return TemplateVariableInput
     */
    public function setListBoxTypeAhead(bool $typeAhead): self
    {
        $this->input_properties['typeAhead'] = $typeAhead;
        return $this;
    }

    /**
     * @param string $typeAheadDelay
     * For types: listbox & listbox-multiple
     * @return TemplateVariableInput
     */
    public function setListBoxTypeAheadDelay(string $typeAheadDelay): self
    {
        $this->input_properties['typeAheadDelay'] = $typeAheadDelay;
        return $this;
    }

    /**
     * @param bool $forceSelection
     * For types: listbox-multiple
     * @return TemplateVariableInput
     */
    public function setForceSelection(bool $forceSelection): self
    {
        $this->input_properties['forceSelection'] = $forceSelection;
        return $this;
    }


    /**
     * @param bool $allowDecimals
     * For types: number
     * @return TemplateVariableInput
     */
    public function setNumberAllowDecimals(bool $allowDecimals): self
    {
        $this->input_properties['allowDecimals'] = $allowDecimals;
        return $this;
    }

    /**
     * @param bool $allowNegative
     * For types: number
     * @return TemplateVariableInput
     */
    public function setNumberAllowNegative(bool $allowNegative): self
    {
        $this->input_properties['allowNegative'] = $allowNegative;
        return $this;
    }

    /**
     * @param int $decimalPrecision
     * For types: number
     * @return TemplateVariableInput
     */
    public function setNumberDecimalPrecision(int $decimalPrecision): self
    {
        $this->input_properties['decimalPrecision'] = $decimalPrecision;
        return $this;
    }

    /**
     * @param string $decimalSeparator
     * For types: number
     * @return TemplateVariableInput
     */
    public function setNumberDecimalSeparator(string $decimalSeparator): self
    {
        $this->input_properties['decimalSeparator'] = $decimalSeparator;
        return $this;
    }

    /**
     * @param string $maxvalue
     * For types: number
     * @return TemplateVariableInput
     */
    public function setNumberMaxvalue(string $maxvalue): self
    {
        $this->input_properties['maxvalue'] = $maxvalue;
        return $this;
    }

    /**
     * @param string $minValue
     * For types: number
     * @return TemplateVariableInput
     */
    public function setNumberMinValue(string $minValue): self
    {
        $this->input_properties['minValue'] = $minValue;
        return $this;
    }


    /**
     * @param bool $showNone
     * For type: resourcelist
     * @return TemplateVariableInput
     */
    public function setResourceListShowNone(bool $showNone): self
    {
        $this->input_properties['showNone'] = $showNone;
        return $this;
    }

    /**
     * @param string $parents
     * For type: resourcelist
     * @return TemplateVariableInput
     */
    public function setResourceListParents(string $parents): self
    {
        $this->input_properties['parents'] = $parents;
        return $this;
    }

    /**
     * @param int $depth
     * For type: resourcelist
     * @return TemplateVariableInput
     */
    public function setResourceListDepth(int $depth): self
    {
        $this->input_properties['depth'] = $depth;
        return $this;
    }

    /**
     * @param bool $includeParent
     * For type: resourcelist
     * @return TemplateVariableInput
     */
    public function setResourceListIncludeParent(bool $includeParent): self
    {
        $this->input_properties['includeParent'] = $includeParent;
        return $this;
    }

    /**
     * @param bool $limitRelatedContext
     * For type: resourcelist
     * @return TemplateVariableInput
     */
    public function setResourceListLimitRelatedContext(bool $limitRelatedContext): self
    {
        $this->input_properties['limitRelatedContext'] = $limitRelatedContext;
        return $this;
    }

    /**
     * @param string $where
     * For type: resourcelist
     * @return TemplateVariableInput
     */
    public function setResourceListWhere(string $where): self
    {
        $this->input_properties['where'] = $where;
        return $this;
    }

    /**
     * @param int $limit
     * For type: resourcelist
     * @return TemplateVariableInput
     */
    public function setResourceListLimit(int $limit): self
    {
        $this->input_properties['limit'] = $limit;
        return $this;
    }


    protected function getDefaultDateProperties()
    {
        // date
        return [
            'disabledDates' => '',
            'disabledDays' => '',
            // date field
            'minDateValue' => '',
            // time
            'minTimeValue' => '',
            // date field
            'maxDateValue' => '',
            // time
            'maxTimeValue' => '',
            'startDay => ',
            'timeIncrement' => '',
            'hideTime' => false
        ];
    }

    protected function getDefaultListboxProperties()
    {
        return [
            // listboxMulti, listboxSingle
            'listWidth' => '',
            'title' => '',
            'listEmptyText' => '',
            /** @var bool ~ require TV */
            'forceSelection' => false,
            'typeAhead' => true,
            'typeAheadDelay' => '',
        ];
    }

    protected function getDefaultNumberProperties()
    {
        return [
            // number
            'allowDecimals' => true,
            'allowNegative' => true,
            'decimalPrecision' => 2,
            'decimalSeparator' => '.',
            'maxvalue' => '',
            'minValue' => ''
        ];
    }

    // email, file, hidden, image, text
    protected function getDefaultTextProperties()
    {
        return [
            'minLength' => '',
            'maxLength' => '',
            'regex' => '',
            'regexText' => '',
        ];
    }

    protected function getDefaultResourceListProperties()
    {
        return [
            // resource list
            'showNone' => false,
            'parents' => '',
            'depth' => 10,
            'includeParent' => false,
            'limitRelatedContext' => false,
            'where' => '',
            'limit' => 0,
        ];
    }

}