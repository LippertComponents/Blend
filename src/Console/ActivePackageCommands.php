<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/1/18
 * Time: 10:54 PM
 */

namespace LCI\Blend\Console;

use LCI\Blend\Blender;
use LCI\MODX\Console\Application;
use LCI\MODX\Console\Command\PackageCommands;
use LCI\MODX\Console\Console;
use LCI\MODX\Console\Helpers\VoidUserInteractionHandler;

class ActivePackageCommands implements PackageCommands
{
    /** @var Console  */
    protected $console;

    /** @var array  */
    protected $commands = [
        'always' => [
            'LCI\Blend\Console\GenerateMigration',
        ],
        'blend_installed' => [
            'LCI\Blend\Console\Migrate',
            'LCI\Blend\Console\Seed'
        ],
        'blend_not_installed' => [

        ],
        'modx_installed' => [
            'LCI\Blend\Console\Blend'
        ],
        'modx_not_installed' => [
            // @TODO command via Gitify
        ]
    ];

    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    /**
     * @return array ~ of Fully qualified names of all command class
     */
    public function getAllCommands()
    {
        $all_commands = [];
        foreach ($this->commands as $group => $commands) {
            foreach ($commands as $command) {
                $all_commands[] = $command;
            }
        }

        return $all_commands;
    }

    /**
     * @return array ~ of Fully qualified names of active command classes. This could differ from all if package creator
     *      has different commands based on the state like the DB. Example has Install and Uninstall, only one would
     *      be active/available depending on the state
     */
    public function getActiveCommands()
    {
        $active_commands = $this->commands['always'];

        if ($this->console->isModxInstalled()) {

            $commands = $this->commands['modx_installed'];
            foreach ($commands as $command) {
                $active_commands[] = $command;
            }

            if ($this->isBlendInstalled() && !$this->isBlendRequireUpdate()) {
                $commands = $this->commands['blend_installed'];
                foreach ($commands as $command) {
                    $active_commands[] = $command;
                }
            }

        } else {
            $commands = $this->commands['modx_not_installed'];
            foreach ($commands as $command) {
                $active_commands[] = $command;
            }
        }

        return $active_commands;
    }

    /**
     * @param \LCI\MODX\Console\Application $application
     * @return \LCI\MODX\Console\Application
     */
    public function loadActiveCommands(Application $application)
    {
        $commands = $this->getActiveCommands();

        foreach ($commands as $command) {
            $class = new $command();

            if (is_object($class)) {
                if (method_exists($class, 'setConsole')) {
                    $class->setConsole($this->console);
                }

                $application->add($class);
            }
        }

        return $application;
    }

    /**
     * @return bool
     */
    public function isBlendInstalled()
    {
        $blend = new Blender($this->console->loadMODX(), new VoidUserInteractionHandler(), []);
        return $blend->isBlendInstalledInModx();
    }

    /**
     * @return bool
     */
    public function isBlendRequireUpdate()
    {
        $blend = new Blender($this->console->loadMODX(), new VoidUserInteractionHandler(), []);
        return $blend->requireUpdate();
    }
}