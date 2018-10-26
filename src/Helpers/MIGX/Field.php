<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/26/18
 * Time: 11:34 AM
 */

namespace LCI\Blend\Helpers\MIGX;

use LCI\Blend\Helpers\TVInput\OptionValues;

class Field
{
    /** @var string  */
    protected $field;

    /** @var string */
    protected $caption;

    /** @var string */
    protected $description;

    /** @var string */
    protected $input_template_variable_name = '';

    /** @var string */
    protected $input_template_variable_type = '';

    /** @var OptionValues  */
    protected $optionValues;

    /** @var bool  */
    protected $migx_media_source = false;

    /** @var array  */
    protected $custom_properties = [];

    /** @var bool  */
    protected $show_in_grid = true;

    /** @var string  */
    protected $grid_header = '';

    /** @var int  */
    protected $grid_width = 100;

    /** @var bool  */
    protected $grid_sortable = true;

    /** @var string  */
    protected $grid_renderer = '';

    /** @var string "editor": "this.textEditor" */
    protected $grid_editor = '';

    /** @var array  */
    protected $grid_custom_properties = [];

    /**
     * Field constructor.
     * @param $field ~ this is the name for your placeholder to use with getImageList and a template
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $field = [
            'field' => $this->field,
            'caption' => $this->caption
        ];

        if (!empty($this->description)) {
            $field['description'] = $this->description;
        }

        if (!empty($this->input_template_variable_name)) {
            // @TODO throw expection if input_template_variable_type has been set
            $field['inputTV'] = $this->input_template_variable_name;
        }

        if (!empty($this->input_template_variable_type)) {
            // @TODO throw expection if input_template_variable_name has been set
            $field['inputTVtype'] = $this->input_template_variable_type;
        }

        if (!empty($this->optionValues) && $this->optionValues instanceof OptionValues) {
            // @TODO review should it throw expection if input_template_variable_name has been set
            $field['inputOptionValues'] = $this->optionValues->toString();
        }

        if (!empty($this->migx_media_source)) {
            $field['sourceFrom'] = 'migx';
        }

        return array_merge($field, $this->custom_properties);
    }

    /**
     * @return bool
     */
    public function isShowInGrid(): bool
    {
        return $this->show_in_grid;
    }

    /**
     * @return array ~ this is the output grid or viewable data that the content editor will always see
     */
    public function getGridArray()
    {
        $grid = [
            'header' => $this->grid_header,
            'dataIndex' => $this->field,
            'width' => $this->grid_width,
            'sortable' => $this->grid_sortable,
        ];

        if (!empty($this->grid_editor)) {
            $grid['editor'] = $this->grid_editor;
        }

        if (!empty($this->grid_renderer)) {
            $grid['renderer'] = $this->grid_renderer;
        }

        return array_merge($grid, $this->grid_custom_properties);
    }

    /**
     * @param string $caption ~ Label for the form field
     * @return $this
     */
    public function setCaption(string $caption): self
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @param string $description ~ Form field description, if empty MIGX will use the description of the inputTV, if any
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $input_template_variable_name ~ the Template Variable name that you would like rendered
     * This is useful if your data type requires any custom functionality (ie, a default value, output options, etc).
     * You can use the same input TV for different fields (ie, if you have an object that has multiple images).
     *
     * Note can only use either setInputTemplateVariableName() or setInputTemplateVariableType() not both
     * @return $this
     */
    public function setInputTemplateVariableName(string $input_template_variable_name): self
    {
        $this->input_template_variable_name = $input_template_variable_name;
        return $this;
    }

    /**
     * @param string $input_template_variable_type ~ any valid MODX template variable type:
     * autotag, checkbox, date, listbox, listbox-multiple, email, file,
     *      hidden, image, number, option [radio], resourcelist, richtext, tag, text, textarea, url
     * @see https://docs.modx.com/revolution/2.x/making-sites-with-modx/customizing-content/template-variables/template-variable-input-types
     * See manager/templates/default/element/tv/renders/input/ files for related code
     *
     * Note can only use either setInputTemplateVariableName() or setInputTemplateVariableType() not both
     * @return $this
     */
    public function setInputTemplateVariableType(string $input_template_variable_type): self
    {
        $this->input_template_variable_type = $input_template_variable_type;
        return $this;
    }

    /**
     * Set to use the Media Source defined on the actual MIGX TV
     * @return $this
     */
    public function useMIGXMediaSource(): self
    {
        $this->migx_media_source = true;
        return $this;
    }

    /**
     * @param string $separator ~ the separator between items
     * @param string $value_separator ~ the separator between label and value
     * @return OptionValues
     */
    public function loadOptionValues(string $separator='||', string $value_separator='==')
    {
        $this->optionValues = new OptionValues($separator, $value_separator);

        return $this->optionValues;
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

    /**
     * @param bool $show_in_grid
     * @return Field
     */
    public function setShowInGrid(bool $show_in_grid): self
    {
        $this->show_in_grid = $show_in_grid;
        return $this;
    }

    /**
     * @param string $grid_header ~
     * @return Field
     */
    public function setGridHeader(string $grid_header): self
    {
        $this->grid_header = $grid_header;
        return $this;
    }

    /**
     * @param int $grid_width
     * @return Field
     */
    public function setGridWidth(int $grid_width): self
    {
        $this->grid_width = $grid_width;
        return $this;
    }

    /**
     * @param bool $grid_sortable
     * @return Field
     */
    public function setGridSortable(bool $grid_sortable): self
    {
        $this->grid_sortable = $grid_sortable;
        return $this;
    }

    /**
     * @param string $grid_renderer
     * @return Field
     */
    public function setGridRenderer(string $grid_renderer): self
    {
        $this->grid_renderer = $grid_renderer;
        return $this;
    }

    /**
     * @param string $grid_editor
     * @return Field
     */
    public function setGridEditor(string $grid_editor): self
    {
        $this->grid_editor = $grid_editor;
        return $this;
    }

    /**
     * Use if a custom MIGX grid(columns) property is needed that is not defined in this object
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setGridCustomProperty($key, $value): self
    {
        $this->custom_properties[$key] = $value;

        return $this;
    }

}