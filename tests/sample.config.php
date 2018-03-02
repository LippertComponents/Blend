<?php
/** @dependency \modX */

// Define where MODX is:
define('MODX_PATH', '/www/');

$config =  MODX_PATH.'/config.core.php';

if (file_exists($config)) {
    require_once $config;
} else {
    $config = MODX_PATH . 'core/config/config.inc.php';
    require_once $config;
}

$modx_class = MODX_CORE_PATH;
if (!file_exists($modx_class)) {
    die('The '.$config.' is not correct, could not find the MODX class in: '.$modx_class);
}
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

define('BLEND_MODX_MIGRATION_PATH', dirname(__DIR__).'/tests/');
define('BLEND_COMPARE_DIRECTORY', dirname(__DIR__).'/tests/compare/');
define('BLEND_TEST_SEEDS_DIR', '2018_01_10_093000');
define('BLEND_CLEAN_UP', true);
