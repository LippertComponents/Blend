<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 2:44 PM
 */

namespace LCI\Blend\Blendable;


class TemplateVariable extends Element
{
    protected $template_names = [];

    protected $detach_template_names = [];

    /** @var string  */
    protected $opt_cache_key = 'elements/template-variables';

    /** @var string ~ the xPDO class name */
    protected $xpdo_simple_object_class = 'modTemplateVar';

    /**
     * @return TemplateVariable
     */
    public function getCurrentVersion()
    {
        /** @var TemplateVariable $element */
        $element = new self($this->modx, $this->blender, $this->getFieldName());
        return $element->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @param string $template_name
     * @param int $rank
     *
     * @return $this
     */
    public function attachToTemplate($template_name, $rank = 0)
    {
        $this->template_names[] = [
            'name' => $template_name,
            'rank' => $rank
        ];
        return $this;
    }

    /**
     * @param string $template_name detach
     * @return $this
     */
    public function detachFromTemplate($template_name)
    {
        $this->detach_template_names[] = $template_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldCaption()
    {
        return $this->blendable_xpdo_simple_object_data['caption'];
    }

    /**
     * @return string
     */
    public function getFieldDefaultText()
    {
        return $this->blendable_xpdo_simple_object_data['default_text'];
    }

    /**
     * @return string
     */
    public function getFieldDisplay()
    {
        return $this->blendable_xpdo_simple_object_data['display'];
    }

    /**
     * @return string
     */
    public function getFieldElements()
    {
        return $this->blendable_xpdo_simple_object_data['elements'];
    }

    /**
     * @return string
     */
    public function getFieldInputProperties()
    {
        return $this->blendable_xpdo_simple_object_data['input_properties'];
    }

    /**
     * @return string
     */
    public function getFieldOutputProperties()
    {
        return $this->blendable_xpdo_simple_object_data['output_properties'];
    }

    /**
     * @return int
     */
    public function getFieldRank()
    {
        return $this->blendable_xpdo_simple_object_data['rank'];
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        return $this->blendable_xpdo_simple_object_data['type'];
    }

    // Setters:
    /**
     * @param string $value  max characters: 80
     * @return $this
     */
    public function setFieldCaption($value)
    {
        $this->blendable_xpdo_simple_object_data['caption'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldDefaultText($value)
    {
        $this->blendable_xpdo_simple_object_data['default_text'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 20
     * @return $this
     */
    public function setFieldDisplay($value)
    {
        $this->blendable_xpdo_simple_object_data['display'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldElements($value)
    {
        $this->blendable_xpdo_simple_object_data['elements'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldInputProperties($value)
    {
        $this->blendable_xpdo_simple_object_data['input_properties'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldOutputProperties($value)
    {
        $this->blendable_xpdo_simple_object_data['output_properties'] = $value;
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
     * @param string $value  max characters: 20, default options:
     *      autotag, checkbox, date, listbox, listbox-multiple, email, file,
     *      hidden, image, number, option, resourcelist, richtext, tag, text, textarea, url
     * @see https://docs.modx.com/revolution/2.x/making-sites-with-modx/customizing-content/template-variables/template-variable-input-types
     * @return $this
     */
    public function setFieldType($value)
    {
        $this->blendable_xpdo_simple_object_data['type'] = $value;
        return $this;
    }

    /**
     * @param string $event_name
     *
     * @return mixed
     */
    public function removeOnEvent($event_name)
    {
        $this->remove_on_event_names = $event_name;
        return $event_name;
    }

    protected function attachRelatedPieces()
    {
        if (count($this->template_names) > 0) {
            $tvs = [];
            foreach ($this->template_names as $template_name_data) {
                // get the TV:
                $template = $this->modx->getObject('modTemplateVar', ['templatename' => $template_name_data['name']]);
                if ($template) {
                    $tvt = $this->modx->getObject('modTemplateVarTemplate', ['tmplvarid' => $this->xPDOSimpleObject->getPrimaryKey(), 'templateid' => $template->getPrimaryKey()]);

                    if (!$tvt) {
                        $tvt = $this->modx->newObject('modTemplateVarTemplate');
                    }
                    $tvt->set('templateid', $template->get('id'));
                    $tvt->set('rand', $template_name_data['rank']);

                    $tvs[] = $tvt;
                } else {
                    $this->error = true;

                }

            }
            $this->xPDOSimpleObject->addMany($tvs, 'TemplateVarTemplates');
        }


        if (count($this->detach_template_names) > 0) {
            $tvs = [];
            foreach ($this->detach_template_names as $template_name) {
                // get the TV:
                $template = $this->modx->getObject('modTemplateVar', ['templatename' => $template_name]);
                if ($template) {
                    $tvt = $this->modx->getObject(
                        'modTemplateVarTemplate',
                        [
                            'templateid' => $template->get('id'),
                            'tmplvarid' => $this->xPDOSimpleObject->get('id')
                        ]
                    );

                    if (is_object($tvt)) {
                        $tvt->remove();
                    }
                } else {
                    $this->error = true;
                }
            }
        }
    }
}
