<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 2:43 PM
 */

namespace LCI\Blend;

use LCI\Blend\Helpers\EmptyUserInteractionHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class BlendConsole extends Application
{
    /**
     * @var \modX
     */
    protected static $modx;

    protected static $logo = 'art/blend.txt';

    /**
     * @return string
     */
    public function getHelp()
    {
        return file_get_contents(__DIR__.'/'.static::$logo). parent::getHelp();
    }

    /**
     * Loads a new modX instance
     *
     * @throws \RuntimeException
     * @return \modX
     */
    public static function loadMODX()
    {
        if (self::$modx) {
            return self::$modx;
        }

        if (!file_exists(MODX_CONFIG_PATH)) {
            throw new \RuntimeException('There does not seem to be a MODX installation here. ');
        }

        require_once(MODX_CONFIG_PATH);
        require_once(MODX_CORE_PATH . 'model/modx/modx.class.php');

        /** @var \modX $modx */
        $modx = new \modX();
        $modx->initialize('mgr');
        $modx->getService('error', 'error.modError', '', '');
        $modx->setLogTarget('ECHO');

        self::$modx = $modx;

        return $modx;
    }

    /**
     * @return bool
     */
    public static function isModxInstalled()
    {
        if (file_exists(MODX_CONFIG_PATH) ) {
            if (defined('M_CORE_PATH') && file_exists(M_CORE_PATH . 'model/modx/modx.class.php')) {
                return true;
            }
            if (defined('MODX_CORE_PATH') && file_exists(MODX_CORE_PATH . 'model/modx/modx.class.php')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function isBlendInstalled()
    {
        $blend = new Blender(self::loadMODX(), new EmptyUserInteractionHandler(), []);
        return $blend->isBlendInstalledInModx();
    }

    /**
     * @return bool
     */
    public static function isBlendRequireUpdate()
    {
        $blend = new Blender(self::loadMODX(), new EmptyUserInteractionHandler(), []);
        return $blend->requireUpdate();
    }

    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

            new InputOption('--help',           '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            //new InputOption('--verbose',        '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.'),
            new InputOption('--version',        '-V', InputOption::VALUE_NONE, 'Display the Blend version.'),
        ));
    }
}