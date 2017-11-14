<?php

/**
 * Auto Generated from Blender
 * Date: 2017/11/10 at 15:29:37 EST -05:00
 */

use \LCI\Blend\Migrations;

class install_blender extends Migrations
{
    /** @var array  */
    protected $blender_table_classes = [
        'BlendMigrations'
    ];

    /** @var array  */
    protected $blender_events = [
        'OnBlendResourceBeforeSave',
        'OnBlendResourceAfterSave',
        'OnBlendSeedResource',
        'OnBlendSeedElement',
        'OnBlendElementBeforeSave',
        'OnBlendElementAfterSave'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // install DB table:
        $manager = $this->modx->getManager();
        // the class table object name
        foreach ($this->blender_table_classes as $table_class) {
            if ($manager->createObjectContainer($table_class)) {
                $this->blender->out($table_class.' table class has been created');

            } else {
                $this->blender->out($table_class.' table class was not created', true);
            }
        }

        // install events:
        foreach ($this->blender_events as $event_name) {
            $this->createSystemEvents($event_name);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // remove DB Table:
        $manager = $this->modx->getManager();
        // the class table object name
        foreach ($this->blender_table_classes as $table_class) {
            if ($manager->removeObjectContainer($table_class)) {
                $this->blender->out($table_class.' table class has been dropped');

            } else {
                $this->blender->out($table_class.' table class was not dropped', true);
            }
        }

        // remove events:
        foreach ($this->blender_events as $event_name) {
            $this->removeSystemEvents($event_name);
        }
    }

    /**
     * @param $event_name
     */
    protected function createSystemEvents($event_name)
    {
        $event = $this->modx->getObject('modEvent', ['name' => $event_name]);
        if (is_object($event)) {
            $this->blender->out($event_name.' event has already been installed', true);
        } else {
            /** @var \modEvent $event */
            $event = $this->modx->newObject('modEvent');
            $event->set('name', $event_name);
            $event->set('service', 1);
            $event->set('groupname', 'Resources');// ??

            if ($event->save()) {
                $this->blender->out($event_name.' event has been installed');

            } else {
                $this->blender->out($event_name.' event did not install', true);
            }
        }
    }

    /**
     * @param $event_name
     */
    protected function removeSystemEvents($event_name)
    {
        $event = $this->modx->getObject('modEvent', ['name' => $event_name]);
        if (is_object($event)) {
            if ($event->remove()) {
                $this->blender->out($event_name . ' event was removed', true);
            } else {
                $this->blender->out($event_name . ' event was not removed', true);
            }
        } else {
            $this->blender->out($event_name . ' event has already been removed', true);
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'Install of Blender';
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignVersion()
    {
        $this->version = '1.0 Dev 2017_11_10_161037';
    }

    /**
     * Method is called on construct, can change to only run this migration for those types
     */
    protected function assignType()
    {
        $this->type = 'master';
    }

    /**
     * Method is called on construct, Child class can override and implement this
     */
    protected function assignTimestamp()
    {
        $this->timestamp = '2017_11_10_152937';
    }
}