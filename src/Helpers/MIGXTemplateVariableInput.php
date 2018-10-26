<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/26/18
 * Time: 11:11 AM
 */

namespace LCI\Blend\Helpers;

use LCI\Blend\Helpers\MIGX\Tab;

class MIGXTemplateVariableInput implements TemplateVariableInputInterface
{
    /** @var string  */
    protected $type = '';

    /** @var bool  */
    protected $allow_blank = true;

    /** @var string|mixed */
    protected $configs = '';

    /** @var array  */
    protected $forms = [];

    /** @var array  */
    protected $tabs = [];

    /** @var string|null  */
    protected $add_button_text = null;

    /** @var string  */
    protected $preview_url = '';

    /** @var string  */
    protected $json_var_key = '';

    /** @var bool  */
    protected $auto_resource_folders = false;

    /** @var array  */
    protected $custom_properties = [];

    /**
     * TemplateVariableInput constructor.
     * @param string $type ~ migx
     */
    public function __construct(string $type='migx')
    {
        $this->type = $type;
    }

    /**
     * @param string $caption
     * @param string|null $wrap_in_form_name ~ advanced usage, HTML form name to wrap tabs in
     * @return Tab
     */
    public function addFormTab(string $caption, $wrap_in_form_name=null)
    {
        $tab = new Tab($caption);

        $form = 'no-form-wrap';
        if (!empty($wrap_in_form_name)) {
            $form = $wrap_in_form_name;
            $this->forms[] = $wrap_in_form_name;
        }

        if (!isset($this->tabs[$form])) {
            $this->tabs[$form] = [];
        }

        $this->tabs[$form][] = $tab;

        return $tab;
    }

    /**
     * @param bool $pretty_json
     * @return array
     */
    public function getInputProperties(bool $pretty_json=false): array
    {
        $input_properties = [
            'allowBlank' => $this->allow_blank,
            'configs' => $this->configs,
            'formtabs' => $this->getFormTabsAsJsonString($pretty_json),
            'columns' => $this->getColumnsAsJsonString($pretty_json),
            'btntext' => $this->add_button_text,
            'previewurl' => $this->preview_url,
            'jsonvarkey' => $this->json_var_key,
            'autoResourceFolders' => $this->auto_resource_folders,
        ];

        return array_merge($input_properties, $this->custom_properties);
    }

    /**
     * @param bool $allow_blank
     * @return $this
     */
    public function setAllowBlank(bool $allow_blank): self
    {
        $this->allow_blank = $allow_blank;
        return $this;
    }

    /**
     * @param mixed|string $configs
     * @return $this
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
        return $this;
    }

    /**
     * @param null|string $add_button_text
     * @return $this
     */
    public function setAddButtonText(string $add_button_text): self
    {
        $this->add_button_text = $add_button_text;
        return $this;
    }

    /**
     * @param string $preview_url ~ a MODX Resource URL and the resource would have a snippet call
     * [[!getImageList? &tvname=`multiitemsgridTv2`]]
     * @return $this
     */
    public function setPreviewUrl(string $preview_url): self
    {
        $this->preview_url = $preview_url;
        return $this;
    }

    /**
     * @param string $json_var_key ~ set this if you have multiple calls on the same preview resource
     * @return $this
     */
    public function setJsonVarKey(string $json_var_key): self
    {
        $this->json_var_key = $json_var_key;
        return $this;
    }

    /**
     * @param bool $auto_resource_folders
     * @return $this
     */
    public function setAutoResourceFolders(bool $auto_resource_folders): self
    {
        $this->auto_resource_folders = $auto_resource_folders;
        return $this;
    }

    /**
     * @param bool $pretty_json
     * @return false|string
     */
    protected function getColumnsAsJsonString($pretty_json=false)
    {
        $json = [];

        foreach ($this->tabs as $form => $tabs) {
            /** @var Tab $tab */
            foreach ($tabs as $tab) {
                $json = $tab->getGridColumns($json);
            }
        }

        if ($pretty_json) {
            return json_encode($json, JSON_PRETTY_PRINT);
        }

        return json_encode($json);
    }

    /**
     * @return string|false
     */
    protected function getFormTabsAsJsonString($pretty_json=false)
    {
        $json = [];

        foreach ($this->tabs as $form => $tabs) {
            $form_tabs = $this->getFormTabsArray($tabs);

            if ($form == 'no-form-wrap') {
                $json = $form_tabs;
            } else {
                $json[] = [
                    'formname' => $form,
                    'formtabs' => $form_tabs
                ];
            }
        }

        if ($pretty_json) {
            return json_encode($json, JSON_PRETTY_PRINT);
        }

        return json_encode($json);
    }

    /**
     * @param array $tabs
     * @return array
     */
    protected function getFormTabsArray($tabs)
    {
        $data = [];

        /** @var Tab $tab */
        foreach ($tabs as $tab) {
            $data[] = $tab->toArray();
        }

        return $data;
    }



}