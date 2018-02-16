<?php

if (!defined('MODX_PATH')) {
    define('MODX_PATH', '/var/www/public/');
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