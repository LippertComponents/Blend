<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 2:44 PM
 */

namespace LCI\Blend;


class TemplateVariable extends Element
{
    protected $template_names = [];

    protected $remove_tv_names = [];

    protected $type = 'text';

    protected $caption = '';

    protected $elements = '';

    protected $display = 'default';

    protected $default_text = '';

    protected $input_properties = '';

    protected $output_properties = '';

    /** @var string ~ the xPDO class name */
    protected $element_class = 'modTemplateVar';

    /**
     * @param string $name
     *
     * @return TemplateVariable
     */
    public function loadCurrentVersion($name)
    {
        /** @var TemplateVariable $element */
        $element = new self($this->modx, $this->blender);
        $element->setSeedTimeDir($this->getTimestamp());
        return $element->loadElementFromName($name);
    }

    /**
     * @param string $template_name
     * @param int $rank
     *
     * @return $this
     */
    public function attachToTemplate($template_name, $rank=0)
    {
        $this->template_names[] = [
            'name' => $template_name,
            'rank' => $rank
        ];
        return $this;
    }

    /**
     * @param string $caption
     *
     * @return $this
     */
    public function setCaption(string $caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @param string $default_text
     *
     * @return $this
     */
    public function setDefaultText(string $default_text)
    {
        $this->default_text = $default_text;
        return $this;
    }
    /**
     * @param string $display
     *
     * @return $this
     */
    public function setDisplay(string $display)
    {
        $this->display = $display;
        return $this;
    }

    /**
     * @param string $elements
     *
     * @return $this
     */
    public function setElements(string $elements)
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * @param string|array $input_properties
     */
    public function setInputProperties($input_properties)
    {
        $this->input_properties = $input_properties;
    }

    /**
     * @param string|array $output_properties
     *
     * @return $this
     */
    public function setOutputProperties($output_properties)
    {
        $this->output_properties = $output_properties;
        return $this;
    }
    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
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

    public function save($overwrite = false)
    {
        $saved = parent::save($overwrite);

        if ($saved) {

        }
        return $saved;
    }

    protected function relatedPieces()
    {
        if (count($this->template_names) > 0) {
            $tvs = [];
            foreach ($this->template_names as $template_name_data) {
                // get the TV:
                $template = $this->modx->getObject('modTemplateVar', ['templatename' => $template_name_data['name']]);
                if ($template) {
                    $tvt = $this->modx->newObject('modTemplateVarTemplate');
                    $tvt->set('templateid', $template->get('id'));
                    $tvt->set('rand', $template_name_data['rank']);

                    $tvs[] = $tvt;
                } else {
                    $this->error = true;

                }

            }
            $this->element->addMany($tvs, 'TemplateVarTemplates');
        }
        // @TODO remove
    }


    protected function setAdditionalElementColumns()
    {
        $this->element->set('type', $this->type);
        $this->element->set('caption', $this->caption);
        $this->element->set('display', $this->display);
        $this->element->set('default_text', $this->default_text);
        $this->element->set('input_properties', $this->input_properties);
        $this->element->set('output_properties', $this->output_properties);
    }
}
