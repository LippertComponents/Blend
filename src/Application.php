<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 2:43 PM
 */

namespace LCI\Blend;

use LCI\MODX\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class Application extends ConsoleApplication
{
    protected static $logo = __DIR__ . '/art/blend.txt';

    protected static $name = 'Blend Console';

    protected static $version = '1.0.0 beta12';

    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            //new InputOption('--verbose',        '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display the Blend version.'),
        ));
    }
}