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
    protected $on_event_names = [];

    protected $remove_on_event_names = [];

    /**
     * @return $this
     */
    public function init()
    {
        parent::init();
        $this->setElementClass('modPlugin');

        return $this;
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
}