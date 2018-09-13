<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 2:44 PM
 */

namespace LCI\Blend\Blendable;


class Plugin extends Element
{
    /** @var string  */
    protected $opt_cache_key = 'elements/plugins';

    /** @var string ~ the xPDO class name */
    protected $xpdo_simple_object_class = 'modPlugin';

    /** @var array  */
    protected $on_event_names = [];

    /** @var array  */
    protected $remove_on_event_names = [];

    /**
     * @return \LCI\Blend\Blendable\Plugin
     */
    public function getCurrentVersion()
    {
        /** @var \LCI\Blend\Blendable\Plugin $plugin */
        $plugin = new self($this->modx, $this->blender, $this->getFieldName());
        return $plugin
            ->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @param string $event_name
     * @param int $priority
     * @param int $property_set
     * @return $this
     */
    public function attachOnEvent($event_name, $priority = 0, $property_set = 0)
    {
        $this->related_data[] = [
            'event' => $event_name,
            'priority' => $priority,
            'propertyset' => $property_set
        ];
        return $this;
    }

    /**
     * @param string $event_name
     *
     * @return $this
     */
    public function removeOnEvent($event_name)
    {
        $this->remove_on_event_names[] = $event_name;
        return $this;
    }
    /**
     * Override in child classes
     */
    protected function loadRelatedData()
    {
        // get all related Events:
        $events = [];
        if ($this->xPDOSimpleObject instanceof \modPlugin) {
            $pluginEvents = $this->xPDOSimpleObject->getMany('PluginEvents');
            /** @var \modPluginEvent $event */
            foreach ($pluginEvents as $event) {
                $data = $event->toArray();
                unset($data['id'], $data['pluginid']);
                $events[] = $data;
            }

            // will be loaded via setOnEvents from blend()
            $this->related_data = $events;
        }

        // Calls on the event: OnBlendLoadRelatedData
        parent::loadRelatedData();
    }

    protected function attachRelatedPiecesAfterSave()
    {
        // remove any:
        $removePluginEvents = $this->xPDOSimpleObject->getMany('PluginEvents', ['events:IN' => $this->remove_on_event_names]);
        foreach ($removePluginEvents as $event) {
            //$event->remove();
        }

        if (count($this->related_data) > 0) {
            $events = [];
            foreach ($this->related_data as $event_data) {

                $pluginEvent = $this->modx->newObject('modPluginEvent');
                $pluginEvent->set('event', $event_data['event']);
                $pluginEvent->set('pluginid', $this->xPDOSimpleObject->get('id'));
                $priority = (!empty($event_data['priority']) ? $event_data['priority'] : 0);
                $pluginEvent->set('priority', (int)$priority);
                $pluginEvent->set('propertyset', (int)(!empty($event_data['propertyset']) ? $event_data['propertyset'] : 0));

                if (!$pluginEvent->save()) {
                    $this->blender->out('Plugin did not attached the event: '.$event_data['event']);
                }
            }
            $this->xPDOSimpleObject->addMany($events, 'PluginEvents');
        }
    }

    /**
     * Called from loadFromArray(), for build from seeds
     *
     * @param mixed|array $data
     *
     * @return $this
     */
    public function setRelatedData($data)
    {
        if (is_array($data)) {
            foreach ($data as $count => $event) {
                if (isset($event['remove']) && $event['remove']) {
                    $this->removeOnEvent($event['event']);

                } else {
                    $this->attachOnEvent($event['event'], $event['priority'], $event['propertyset']);
                }
            }
        }

        return $this;
    }

    /**
     * @var string $type blend or revert
     */
    protected function seedRelated($type = 'blend')
    {
        // get all related Events:
        $events = [];
        if (is_object($this->xPDOSimpleObject)) {
            $pluginEvents = $this->xPDOSimpleObject->getMany('PluginEvents');
            /** @var \modPluginEvent $event */
            foreach ($pluginEvents as $event) {
                $data = $event->toArray();
                unset($data['id'], $data['pluginid']);
                $events[] = $data;

            }
        }
        // will be loaded via setOnEvents from blend()
        $this->related_data = $events;
    }
}
