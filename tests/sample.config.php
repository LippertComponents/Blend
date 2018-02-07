<?php
// Need to define the path the MODX core config file
require_once '/www/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

define('BLEND_MODX_MIGRATION_PATH', dirname(__DIR__).'/tests/');
define('BLEND_COMPARE_DIRECTORY', dirname(__DIR__).'/tests/compare/');
define('BLEND_TEST_SEEDS_DIR', '2018_01_10_093000');
define('BLEND_CLEAN_UP', true);
