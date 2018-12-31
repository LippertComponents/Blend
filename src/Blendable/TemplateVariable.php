<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 2:44 PM
 */

namespace LCI\Blend\Blendable;


use LCI\Blend\Helpers\MIGXTemplateVariableInput;
use LCI\Blend\Helpers\TemplateVariableInput;
use LCI\Blend\Helpers\TVInput\OptionValues;

class TemplateVariable extends Element
{
    protected $template_names = [];

    protected $detach_template_names = [];

    /** @var array - [name => context, ...] */
    protected $media_sources = [];

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
     * @return TemplateVariableInput
     */
    public function getInputPropertyHelper()
    {
        return new TemplateVariableInput($this->getFieldType());
    }

    /**
     * @param string $type
     * @return MIGXTemplateVariableInput
     */
    public function getMIGXInputPropertyHelper(string $type='migx')
    {
        $this->setFieldType($type);
        return new MIGXTemplateVariableInput($this->getFieldType());
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
     * @param string $value ~ this is Input Option Values, this is setting it raw
     * @deprecated use makeInputOptionValues()
     * @return $this
     */
    public function setFieldElements($value)
    {
        $this->blendable_xpdo_simple_object_data['elements'] = $value;
        return $this;
    }

    /**
     * @return OptionValues
     */
    public function makeInputOptionValues()
    {
        $this->blendable_xpdo_simple_object_data['elements'] = new OptionValues();

        return $this->blendable_xpdo_simple_object_data['elements'];
    }

    /**
     * @param array $value
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
     * @param string $name ~ name of the media source
     * @param string $context - default is web
     * @return $this
     */
    public function setMediaSource($name = 'Filesystem', $context='web')
    {
        $this->media_sources[$name] = $context;
        if ($context == 'web') {
            // This probably is not needed any longer
            $this->blendable_xpdo_simple_object_data['source'] = $name;
        }

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
     *      hidden, image, number, option [radio], resourcelist, richtext, tag, text, textarea, url
     * @see https://docs.modx.com/revolution/2.x/making-sites-with-modx/customizing-content/template-variables/template-variable-input-types
     * @return $this
     */
    public function setFieldType($value)
    {
        $this->blendable_xpdo_simple_object_data['type'] = $value;
        return $this;
    }

    protected function attachRelatedPieces()
    {
        if (count($this->template_names) > 0) {
            $tvs = [];
            foreach ($this->template_names as $template_name_data) {
                // get the Template:
                $template = $this->modx->getObject('modTemplate', ['templatename' => $template_name_data['name']]);
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
                // get the Template:
                $template = $this->modx->getObject('modTemplate', ['templatename' => $template_name]);
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

    /**
     * This method is called just after a successful blend/save()
     */
    protected function attachRelatedPiecesAfterSave()
    {
        foreach ($this->media_sources as $name => $context) {
            /** @var /modMediaSourceElement $modMediaSourceElement */
            $modMediaSourceElement = $this->modx->getObject(
                'sources.modMediaSourceElement',
                [
                    'object_class' => 'modTemplateVar',
                    'object' => $this->xPDOSimpleObject->get('id'),
                    'context_key' => $context
                ]
            );

            if (!is_object($modMediaSourceElement)) {
                $modMediaSourceElement = $this->modx->newObject('sources.modMediaSourceElement');

                $modMediaSourceElement->set('object_class', 'modTemplateVar');
                $modMediaSourceElement->set('object', $this->xPDOSimpleObject->get('id'));
                $modMediaSourceElement->set('context_key', $context);
            }

            $modMediaSourceElement->set('source', $this->convertSource($name));

            if (!$modMediaSourceElement->save()) {
                $this->blender->out('Template variable ' .$this->xPDOSimpleObject->get('name').
                    'did not attached the media source: '.$name);
            }
        }
    }
}
