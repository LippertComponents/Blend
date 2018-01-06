<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 2:44 PM
 */

namespace LCI\Blend;


class Plugin extends Element
{
    /** @var string ~ the xPDO class name */
    protected $element_class = 'modPlugin';

    /** @var array  */
    protected $on_event_names = [];

    /** @var array  */
    protected $remove_on_event_names = [];

    /**
     * @param string $name
     *
     * @return Plugin
     */
    public function loadCurrentVersion($name)
    {
        /** @var Plugin $element */
        $element = new self($this->modx, $this->blender);
        $element->setSeedTimeDir($this->getTimestamp());
        return $element->loadElementFromName($name);
    }

    /**
     * @param string $event_name
     * @param int $priority
     * @param int $property_set
     * @return $this
     */
    public function attachOnEvent($event_name, $priority=0, $property_set=0)
    {
        $this->on_event_names[] = [
            'event' => $event_name,
            'priority' => $priority,
            'propertyset' => $property_set
        ];
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
    /**
     * Override in child classes
     */
    protected function loadRelatedData()
    {
        // get all related Events:
        $events = [];
        if ($this->element instanceof \modPlugin) {
            $pluginEvents = $this->element->getMany('PluginEvents');
            /** @var \modPluginEvent $event */
            foreach ($pluginEvents as $event) {
                $events[] = $event->toArray();
            }
            // will be loaded via setOnEvents from blend()
            $this->related_data = $events;
        }
    }

    protected function relatedPieces()
    {
        if (count($this->on_event_names) > 0) {
            $events = [];
            foreach ($this->on_event_names as $event_data) {
                $event = $this->modx->newObject('modPluginEvent');
                $event->fromArray($event_data);

                $events[] = $event;
            }
            $this->element->addMany($events, 'PluginEvents');
        }
        // @TODO remove
    }

    /**
     * Called from loadFromArray(), for build from seeds
     *
     * @param mixed|array $data
     *
     * @return $this
     */
    protected function setRelatedData($data)
    {
        if (is_array($data)) {
            foreach ($data as $event) {
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
     * @param \modPlugin $plugin
     *
     * @return \modPlugin
     */
    protected function seedRelated($plugin)
    {
        // get all related Events:
        $events = [];
        $pluginEvents = $plugin->getMany('PluginEvents');
        /** @var \modPluginEvent $event */
        foreach ($pluginEvents as $event) {
            $events[] = $event->toArray();

        }
        // will be loaded via setOnEvents from blend()
        $this->related_data = $events;

        return $plugin;
    }
}
