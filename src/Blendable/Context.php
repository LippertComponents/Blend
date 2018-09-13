<?php

namespace LCI\Blend\Blendable;
use LCI\Blend\Blender;

/**
 * Class Context
 * @package LCI\Blend\Blendable
 */
class Context extends Blendable
{
    use DescriptionGetterAndSetter;

    /** @var string  */
    protected $opt_cache_key = 'contexts';

    /** @var string ex: modContext */
    protected $xpdo_simple_object_class = 'modContext';

    /** @var string  */
    protected $unique_key_column = 'key';

    /** @var array ~ this should match data to be inserted via xPDO, ex [column_name => value, ...] */
    protected $blendable_xpdo_simple_object_data = [
        'description' => '',
        'key' => '',
        'name' => '',
        'rank' => 0,
    ];

    /** @var array  */
    protected $portable_settings = [];

    protected $remove_settings = [];

    /**
     * Resource constructor.
     *
     * @param \modx $modx
     * @param Blender $blender
     * @param string $key ~ the context key
     */
    public function __construct(\modx $modx, Blender $blender, $key = '')
    {
        $this->setFieldKey($key);
        parent::__construct($modx, $blender, $key);

        $additional = explode(',', $this->modx->getOption('blend.portable.templateVariables.mediaSources'));
        if (count($additional) > 0) {
            foreach ($additional as $tv_name) {
                $this->portable_settings[$tv_name] = 'media_source';
            }
        }

        $additional = explode(',', $this->modx->getOption('blend.portable.templateVariables.resources'));
        if (count($additional) > 0) {
            foreach ($additional as $tv_name) {
                $this->portable_settings[$tv_name] = 'resource';
            }
        }

        $additional = explode(',', $this->modx->getOption('blend.portable.templateVariables.templates'));
        if (count($additional) > 0) {
            foreach ($additional as $tv_name) {
                $this->portable_settings[$tv_name] = 'template';
            }
        }
    }

    /**
     * @param string $type ~ seed or revert
     * @return string
     */
    public function getSeedKey($type = 'seed')
    {
        $name = $this->getFieldKey();
        $key = $this->blender->getSeedKeyFromName($name);

        switch ($type) {
            case 'revert':
                $seed_key = 'revert-'.$key;
                break;

            case 'seed':
                // no break
            default:
                $seed_key = $key;
        }

        return $seed_key;
    }

    // Column Getters:
    /**
     * @return string
     */
    public function getFieldKey()
    {
        return $this->blendable_xpdo_simple_object_data['key'];
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->blendable_xpdo_simple_object_data['name'];
    }

    /**
     * @return int
     */
    public function getFieldRank()
    {
        return $this->blendable_xpdo_simple_object_data['rank'];
    }

    // Column Setters:

    /**
     * @param string $value  max characters: 100
     * @return $this
     */
    public function setFieldKey($value)
    {
        $this->blendable_xpdo_simple_object_data['key'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 191
     * @return $this
     */
    public function setFieldName($value)
    {
        $this->blendable_xpdo_simple_object_data['name'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldRank($value)
    {
        $this->blendable_xpdo_simple_object_data['rank'] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $xtype
     * @param string $area
     * @param string $namespace
     * @return $this
     */
    public function addSetting($key, $value, $xtype = 'textfield', $area = '', $namespace = 'core')
    {
        // @TODO how to use the SystemSetting class to make blendable context settings and IDE helpers
        $this->related_data['settings'][] = [
            'area' => $area,
            'namespace' => $namespace,
            'key' => $key,
            'value' => $value,
            'xtype' => $xtype,
        ];
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeSetting($key)
    {
        $this->remove_settings[] = $key;
        return $this;
    }


    /**
     * @return Blendable
     */
    public function getCurrentVersion()
    {
        /** @var \LCI\Blend\Blendable\Resource $resource */
        $resource = new self($this->modx, $this->blender, $this->getFieldKey());
        return $resource
            ->setSeedsDir($this->getSeedsDir());
    }

    /**
     * Override in child classes
     */
    protected function loadRelatedData()
    {
        $settings = [];
        $contextSettings = [];
        if (is_object($this->xPDOSimpleObject)) {
            /** @var array of \modContextSetting $contextSettings */
            $contextSettings = $this->xPDOSimpleObject->getMany('ContextSettings');
        }
        /** @var \modContextSetting $setting */
        foreach ($contextSettings as $setting) {
            $settings[] = $this->makePortableData($setting->toArray());
        }

        // @TODO modContextResource or should this be on Resource
        $this->related_data = [
            'settings' => $settings
        ];

        // Calls on the event: OnBlendLoadRelatedData
        parent::loadRelatedData();
    }


    /**
     * @param array $setting
     * @return bool|string
     */
    protected function getPortableType($setting)
    {
        $type = false;
        switch ($setting['xtype']) {
            case 'modx-combo-template':
                $type = 'template';
                break;

            case 'modx-combo-source':
                $type = 'media-source';
                break;

            default:
                if (isset($this->portable_settings[$setting['key']])) {
                    $type = $this->portable_settings[$setting['key']];
                }
        }
        return $type;
    }

    /**
     * @param array $setting
     * @return array
     */
    protected function makePortableData($setting)
    {
        $type = $this->getPortableType($setting);

        switch ($type) {
            case 'media_source':
                $mediaSource = $this->modx->getObject('modMediaSource', $setting['value']);
                if (is_object($mediaSource)) {
                    $setting['portable_type'] = 'media_source';
                    $setting['portable_value'] = $mediaSource->get('name');
                }
                break;

            case 'resource':
                $setting['portable_type'] = 'resource';
                $setting['portable_value'] = $this->blender->getResourceSeedKeyFromID($setting['value']);
                break;

            case 'template':
                $template = $this->modx->getObject('modTemplate', $setting['value']);
                if (is_object($template)) {
                    $setting['portable_type'] = 'media_source';
                    $setting['portable_value'] = $template->get('templatename');
                }
                break;
        }

        return $setting;
    }

    /**
     * Create convert methods for any portable data column that needs to be converted to an int for a related primary key
     */

    /**
     * @param array $setting
     * @return string|int|mixed $value
     */
    protected function convertToLocalData($setting)
    {
        $value = $setting['value'];
        if (is_array($setting) && isset($setting['portable_type']) && isset($setting['portable_value'])) {
            switch ($setting['portable_type']) {
                case 'media_source':
                    $mediaSource = $this->modx->getObject('modMediaSource', ['name' => $setting['portable_value']]);
                    if (is_object($mediaSource)) {
                        $value = $mediaSource->get('id');
                    }
                    break;

                case 'resource':
                    $value = $this->blender->getResourceIDFromSeedKey($setting['portable_value']['seed_key'], $setting['portable_value']['context']);
                    break;

                case 'template':
                    $template = $this->modx->getObject('modTemplate', ['templatename' => $setting['portable_value']]);
                    if (is_object($template)) {
                        $value = $template->get('id');
                    }
                    break;
            }
        }

        return $value;
    }

    /**
     * This method is called just after a successful blend/save()
     */
    protected function attachRelatedPiecesAfterSave()
    {
        if (isset($this->related_data['settings']) && is_array($this->related_data['settings']) && count($this->related_data['settings']) > 0) {
            foreach ($this->related_data['settings'] as $setting) {

                $contextSetting = $this->modx->getObject('modContextSetting', ['context_key' => $this->getFieldKey(), 'key' => $setting['key']]);
                if (!is_object($contextSetting)) {
                    $contextSetting = $this->modx->newObject('modContextSetting');
                }
                $contextSetting->fromArray($setting);
                $contextSetting->set('context_key', $this->getFieldKey());
                $contextSetting->set('key', $setting['key']);
                $contextSetting->set('value', $this->convertToLocalData($setting));
                $contextSetting->save();
            }
        }

        foreach ($this->remove_settings as $setting_key) {

            $contextSetting = $this->modx->getObject('modContextSetting', ['context_key' => $this->getFieldKey(), 'key' => $setting_key]);
            if (is_object($contextSetting)) {
                $contextSetting->remove();
            }
        }

        // @TODO modContextResource or should this be on Resource
    }
}
