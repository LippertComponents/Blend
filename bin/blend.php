<?php

ini_set('display_errors', 1);

use LCI\Blend\Application;
use LCI\MODX\Console\Console;

$autoloader_possible_paths = [
    // if cloned from git:
    dirname(__DIR__).'/vendor/autoload.php',
    // if installed via composer:
    dirname(dirname(dirname(__DIR__))).'/autoload.php',
];
foreach ($autoloader_possible_paths as $autoloader_path) {
    if (file_exists($autoloader_path)) {
        require_once $autoloader_path;
        break;
    }
}
/*
if (!class_exists('\LCI\Blend\Console\Migrate', false)) {
    die('Uh oh, it looks like dependencies have not yet been installed with Composer.'.PHP_EOL.
        'Please follow the installation instructions at https://packagist.org/packages/lci/blend'.PHP_EOL);
}
*/

/** @var string $local_config ~ path to allow you to override/set the MODX include paths */
$local_config = __DIR__.'/config.php';

if (file_exists($local_config)) {
    require_once $local_config;

} else {
    // @TODO refactor and use .env file and console->findMODX()
    // search for MODX:
    $modx_possible_paths = [
        // if cloned from git, up from /www like /home/blend in MODXCloud
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'config.core.php',
        // if cloned from git, up from /www like /home/ in MODXCloud
        dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'config.core.php',
        // if cloned from git, into /
        dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'config.core.php',
        // if cloned from git, into /core/components/blend/
        dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'config.core.php',

        // if installed via composer, up from /www like /home/blend in MODXCloud
        dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'config.core.php',
        // if installed via composer, up from /www like /home in MODXCloud
        dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'config.core.php',
        // if installed via composer, into /
        dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'config.core.php',
        // if installed via composer, into /core/components/blend/
        dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))) . DIRECTORY_SEPARATOR . 'config.core.php',
    ];
    foreach ($modx_possible_paths as $modx_path) {
        if (file_exists($modx_path)) {
            if (!defined('MODX_PATH')) {
                define('MODX_PATH', rtrim(dirname($modx_path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
            }
            break;
        }
    }

    if (!defined('MODX_CONFIG_PATH')) {
        define('MODX_CONFIG_PATH', MODX_PATH.'config.core.php');
    }

    if (!defined('M_CORE_PATH')) {
        define('M_CORE_PATH', MODX_PATH.'core/');
    }

    // Where you will write your migration project/site
    if (!defined('BLEND_MY_MIGRATION_PATH')) {
        define('BLEND_MY_MIGRATION_PATH', M_CORE_PATH.'components/blend/');
    }

    // Future for Upgrading MODX via blend
    if (!defined('BLEND_MODX_MIGRATION_PATH')) {
        define('BLEND_MODX_MIGRATION_PATH', M_CORE_PATH.'components/blend-modx/');
    }

    // Future for Upgrading MODX Packages via blend
    if (!defined('BLEND_PACKAGE_MIGRATION_PATH')) {
        define('BLEND_PACKAGE_MIGRATION_PATH', M_CORE_PATH.'components/blend-packages/');
    }
}


/**
 * Ensure the timezone is set;
 */
if (version_compare(phpversion(),'5.3.0') >= 0) {
    $tz = @ini_get('date.timezone');
    if (empty($tz)) {
        date_default_timezone_set(@date_default_timezone_get());
    }
}

/** @var \LCI\MODX\Console\Console $console */
$console = new Console();

$console->registerPackageCommands('LCI\Blend\Console\ActivePackageCommands');

/** @var \LCI\Blend\Application $application */
$application = new Application($console);
$application->loadCommands();

return $application;
