<?php

ini_set('display_errors', 1);

use LCI\Blend\BlendConsole;
use LCI\Blend\Console\Blend;
use LCI\Blend\Console\Modx\Install as ModxInstall;
use LCI\Blend\Console\Modx\Upgrade as ModxUpgrade;
use LCI\Blend\Console\Modx\InstallPackage as ModxInstallPackage;
use LCI\Blend\Console\Modx\RefreshCache;
use LCI\Blend\Console\Migrate;
use LCI\Blend\Console\Seed;


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
                define('MODX_PATH', dirname($modx_path));
            }
            break;
        }
    }

    if (!defined('MODX_CONFIG_PATH')) {
        define('MODX_CONFIG_PATH', MODX_PATH.'config.core.php');
    }

    if (!defined('MODX_CORE_PATH')) {
        define('MODX_CORE_PATH', MODX_PATH.'core/');
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

/** @var \LCI\Blend\BlendConsole $application */
$application = new BlendConsole('Bend', '1.0.0dev');
// need a check if MODX is installed:
if (BlendConsole::isModxInstalled()) {
    $application->add(new Blend);
    if (BlendConsole::isBlendInstalled() && !BlendConsole::isBlendRequireUpdate()) {
        $application->add(new Migrate);
        $application->add(new Seed);
    }

    //$application->add(new ModxUpgrade);
    //$application->add(new ModxInstallPackage);
    $application->add(new RefreshCache);

} else {
    //die('MODX is not install, please install MODX or create a config.php file with the proper paths');
    $application->add(new ModxInstall);

}
return $application;
