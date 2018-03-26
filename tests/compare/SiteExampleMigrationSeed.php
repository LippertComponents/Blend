<?php

/**
 * Auto Generated from Blender
 * Date: 2018/03/26 at 19:07:40 UTC +00:00
 */

use \LCI\Blend\Migrations;

class SiteExampleMigrationSeed extends Migrations
{
    /** @var array */
    protected $site_data = array (
      'mediaSources' => 
      array (
        0 => 'Filesystem',
        1 => 'mediaSourceSiteExample',
      ),
      'contexts' => 
      array (
        1 => 'site',
      ),
      'chunks' => 
      array (
        0 => 'chunkSiteExample',
      ),
      'plugins' => 
      array (
        0 => 'pluginSiteExample',
      ),
      'resources' => 
      array (
        'web' => 
        array (
          0 => 'index',
          1 => 'site-example-resource',
        ),
      ),
      'snippets' => 
      array (
        0 => 'snippetSiteExample',
      ),
      'systemSettings' => 
      array (
        0 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'access_category_enabled',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        1 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'access_context_enabled',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        2 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'access_resource_group_enabled',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        3 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'allow_forward_across_contexts',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        4 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'allow_manager_login_forgot_password',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        5 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'allow_multiple_emails',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        6 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'allow_tags_in_post',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        7 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'allow_tv_eval',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        8 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'anonymous_sessions',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        9 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'archive_with',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        10 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'automatic_alias',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        11 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'auto_check_pkg_updates',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        12 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'auto_check_pkg_updates_cache_expire',
            'namespace' => 'core',
            'value' => '15',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        13 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'auto_isfolder',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        14 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'auto_menuindex',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        15 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'base_help_url',
            'namespace' => 'core',
            'value' => '//docs.modx.com/display/revolution20/',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        16 => 
        array (
          'columns' => 
          array (
            'area' => 'Blend',
            'key' => 'blend.portable.systemSettings.mediaSources',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        17 => 
        array (
          'columns' => 
          array (
            'area' => 'Blend',
            'key' => 'blend.portable.systemSettings.resources',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        18 => 
        array (
          'columns' => 
          array (
            'area' => 'Blend',
            'key' => 'blend.portable.systemSettings.templates',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        19 => 
        array (
          'columns' => 
          array (
            'area' => 'Blend',
            'key' => 'blend.portable.templateVariables.mediaSources',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        20 => 
        array (
          'columns' => 
          array (
            'area' => 'Blend',
            'key' => 'blend.portable.templateVariables.resources',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        21 => 
        array (
          'columns' => 
          array (
            'area' => 'Blend',
            'key' => 'blend.portable.templateVariables.templates',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        22 => 
        array (
          'columns' => 
          array (
            'area' => 'Blend',
            'key' => 'blend.version',
            'namespace' => 'core',
            'value' => '1.0.0 beta',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        23 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'blend_system_setting_test',
            'namespace' => 'core',
            'value' => 'This is only a test, I am safe to delete',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        24 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'blocked_minutes',
            'namespace' => 'core',
            'value' => '60',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        25 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_action_map',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        26 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_alias_map',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        27 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_context_settings',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        28 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_db',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        29 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_db_expires',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        30 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_db_session',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        31 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_db_session_lifetime',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        32 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_default',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        33 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_disabled',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        34 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_expires',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        35 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_format',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        36 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_handler',
            'namespace' => 'core',
            'value' => 'xPDO\\Cache\\xPDOFileCache',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        37 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_lang_js',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        38 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_lexicon_topics',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        39 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_noncore_lexicon_topics',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        40 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_resource',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        41 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_resource_expires',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        42 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_scripts',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        43 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'cache_system_settings',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        44 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'clear_cache_refresh_trees',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        45 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'compress_css',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        46 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'compress_js',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        47 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'compress_js_max_files',
            'namespace' => 'core',
            'value' => '10',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        48 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'confirm_navigation',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        49 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'container_suffix',
            'namespace' => 'core',
            'value' => '/',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        50 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'context_tree_sort',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        51 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'context_tree_sortby',
            'namespace' => 'core',
            'value' => 'rank',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        52 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'context_tree_sortdir',
            'namespace' => 'core',
            'value' => 'ASC',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        53 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'cultureKey',
            'namespace' => 'core',
            'value' => 'en',
            'xtype' => 'modx-combo-language',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        54 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'date_timezone',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        55 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'debug',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        56 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'default_content_type',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'modx-combo-content-type',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        57 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'default_context',
            'namespace' => 'core',
            'value' => 'web',
            'xtype' => 'modx-combo-context',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        58 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'default_duplicate_publish_option',
            'namespace' => 'core',
            'value' => 'preserve',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        59 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'default_media_source',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'modx-combo-source',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        60 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'default_per_page',
            'namespace' => 'core',
            'value' => '20',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        61 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'default_template',
            'namespace' => 'core',
            'value' => 
            array (
              'type' => 'template',
              'portable_value' => 'Base Template',
              'value' => '1',
            ),
            'xtype' => 'modx-combo-template',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        62 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'default_username',
            'namespace' => 'core',
            'value' => '(anonymous)',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        63 => 
        array (
          'columns' => 
          array (
            'area' => 'editor',
            'key' => 'editor_css_path',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        64 => 
        array (
          'columns' => 
          array (
            'area' => 'editor',
            'key' => 'editor_css_selectors',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        65 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'emailsender',
            'namespace' => 'core',
            'value' => 'need-email@email.com',
            'xtype' => 'textfield',
            'editedon' => '2018-03-01 19:38:58',
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        66 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'emailsubject',
            'namespace' => 'core',
            'value' => 'Your login details',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        67 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'enable_dragdrop',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        68 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'enable_gravatar',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        69 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'error_page',
            'namespace' => 'core',
            'value' => 
            array (
              'type' => 'resource',
              'portable_value' => 
              array (
                'context' => 'web',
                'seed_key' => 'index',
              ),
              'value' => '1',
            ),
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        70 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'failed_login_attempts',
            'namespace' => 'core',
            'value' => '5',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        71 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'feed_modx_news',
            'namespace' => 'core',
            'value' => 'http://feeds.feedburner.com/modx-announce',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        72 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'feed_modx_news_enabled',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        73 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'feed_modx_security',
            'namespace' => 'core',
            'value' => 'http://forums.modx.com/board.xml?board=294',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        74 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'feed_modx_security_enabled',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        75 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'fe_editor_lang',
            'namespace' => 'core',
            'value' => 'en',
            'xtype' => 'modx-combo-language',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        76 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'filemanager_path',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        77 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'filemanager_path_relative',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        78 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'filemanager_url',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        79 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'filemanager_url_relative',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        80 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'forgot_login_email',
            'namespace' => 'core',
            'value' => '<p>Hello [[+username]],</p>
    <p>A request for a password reset has been issued for your MODX user. If you sent this, you may follow this link and use this password to login. If you did not send this request, please ignore this email.</p>
    
    <p>
        <strong>Activation Link:</strong> [[+url_scheme]][[+http_host]][[+manager_url]]?modahsh=[[+hash]]<br />
        <strong>Username:</strong> [[+username]]<br />
        <strong>Password:</strong> [[+password]]<br />
    </p>
    
    <p>After you log into the MODX Manager, you can change your password again, if you wish.</p>
    
    <p>Regards,<br />Site Administrator</p>',
            'xtype' => 'textarea',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        81 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'form_customization_use_all_groups',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        82 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'forward_merge_excludes',
            'namespace' => 'core',
            'value' => 'type,published,class_key',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        83 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_lowercase_only',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        84 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_max_length',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        85 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_realtime',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        86 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_restrict_chars',
            'namespace' => 'core',
            'value' => 'pattern',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        87 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_restrict_chars_pattern',
            'namespace' => 'core',
            'value' => '/[\\0\\x0B\\t\\n\\r\\f\\a&=+%#<>"~:`@\\?\\[\\]\\{\\}\\|\\^\'\\\\]/',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        88 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_strip_element_tags',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        89 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_translit',
            'namespace' => 'core',
            'value' => 'none',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        90 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_translit_class',
            'namespace' => 'core',
            'value' => 'translit.modTransliterate',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        91 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_translit_class_path',
            'namespace' => 'core',
            'value' => '{core_path}components/',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        92 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_trim_chars',
            'namespace' => 'core',
            'value' => '/.-_',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        93 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_word_delimiter',
            'namespace' => 'core',
            'value' => '-',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        94 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_alias_word_delimiters',
            'namespace' => 'core',
            'value' => '-_',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        95 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_urls',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        96 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'friendly_urls_strict',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        97 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'global_duplicate_uri_check',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        98 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'hidemenu_default',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        99 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'inline_help',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        100 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'link_tag_scheme',
            'namespace' => 'core',
            'value' => '-1',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        101 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'locale',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        102 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'lock_ttl',
            'namespace' => 'core',
            'value' => '360',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        103 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'login_background_image',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        104 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'login_help_button',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        105 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'login_logo',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        106 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'log_level',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        107 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'log_snippet_not_found',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        108 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'log_target',
            'namespace' => 'core',
            'value' => 'FILE',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        109 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_charset',
            'namespace' => 'core',
            'value' => 'UTF-8',
            'xtype' => 'modx-combo-charset',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        110 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_encoding',
            'namespace' => 'core',
            'value' => '8bit',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        111 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_auth',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        112 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_helo',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        113 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_hosts',
            'namespace' => 'core',
            'value' => 'localhost',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        114 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_keepalive',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        115 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_pass',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'text-password',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        116 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_port',
            'namespace' => 'core',
            'value' => '587',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        117 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_prefix',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        118 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_single_to',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        119 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_timeout',
            'namespace' => 'core',
            'value' => '10',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        120 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_smtp_user',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        121 => 
        array (
          'columns' => 
          array (
            'area' => 'mail',
            'key' => 'mail_use_smtp',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        122 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'main_nav_parent',
            'namespace' => 'core',
            'value' => 'topnav',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        123 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_date_format',
            'namespace' => 'core',
            'value' => 'Y-m-d',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        124 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'manager_direction',
            'namespace' => 'core',
            'value' => 'ltr',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        125 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_favicon_url',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        126 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_js_cache_file_locking',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        127 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_js_cache_max_age',
            'namespace' => 'core',
            'value' => '3600',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        128 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_js_document_root',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        129 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_js_zlib_output_compression',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        130 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'manager_language',
            'namespace' => 'core',
            'value' => 'en',
            'xtype' => 'modx-combo-language',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        131 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'manager_lang_attribute',
            'namespace' => 'core',
            'value' => 'en',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        132 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'manager_login_url_alternate',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        133 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_theme',
            'namespace' => 'core',
            'value' => 'default',
            'xtype' => 'modx-combo-manager-theme',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        134 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_time_format',
            'namespace' => 'core',
            'value' => 'g:i a',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        135 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_use_fullname',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        136 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'manager_week_start',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        137 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'mgr_source_icon',
            'namespace' => 'core',
            'value' => 'icon-folder-open-o',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        138 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'mgr_tree_icon_context',
            'namespace' => 'core',
            'value' => 'tree-context',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        139 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'modx_browser_default_sort',
            'namespace' => 'core',
            'value' => 'name',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        140 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'modx_browser_default_viewmode',
            'namespace' => 'core',
            'value' => 'grid',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        141 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'modx_browser_tree_hide_files',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        142 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'modx_browser_tree_hide_tooltips',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        143 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'modx_charset',
            'namespace' => 'core',
            'value' => 'UTF-8',
            'xtype' => 'modx-combo-charset',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        144 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'parser_recurse_uncacheable',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        145 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'password_generated_length',
            'namespace' => 'core',
            'value' => '8',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        146 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'password_min_length',
            'namespace' => 'core',
            'value' => '8',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        147 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_allow_src_above_docroot',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        148 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_cache_maxage',
            'namespace' => 'core',
            'value' => '30',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        149 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_cache_maxfiles',
            'namespace' => 'core',
            'value' => '10000',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        150 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_cache_maxsize',
            'namespace' => 'core',
            'value' => '100',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        151 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_cache_source_enabled',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        152 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_document_root',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        153 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_error_bgcolor',
            'namespace' => 'core',
            'value' => 'CCCCFF',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        154 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_error_fontsize',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        155 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_error_textcolor',
            'namespace' => 'core',
            'value' => 'FF0000',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        156 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_far',
            'namespace' => 'core',
            'value' => 'C',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        157 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_imagemagick_path',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        158 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nohotlink_enabled',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        159 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nohotlink_erase_image',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        160 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nohotlink_text_message',
            'namespace' => 'core',
            'value' => 'Off-server thumbnailing is not allowed',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        161 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nohotlink_valid_domains',
            'namespace' => 'core',
            'value' => '{http_host}',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        162 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nooffsitelink_enabled',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        163 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nooffsitelink_erase_image',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        164 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nooffsitelink_require_refer',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        165 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nooffsitelink_text_message',
            'namespace' => 'core',
            'value' => 'Off-server linking is not allowed',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        166 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nooffsitelink_valid_domains',
            'namespace' => 'core',
            'value' => '{http_host}',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        167 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_nooffsitelink_watermark_src',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        168 => 
        array (
          'columns' => 
          array (
            'area' => 'phpthumb',
            'key' => 'phpthumb_zoomcrop',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        169 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'preserve_menuindex',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        170 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'principal_targets',
            'namespace' => 'core',
            'value' => 'modAccessContext,modAccessResourceGroup,modAccessCategory,sources.modAccessMediaSource,modAccessNamespace',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        171 => 
        array (
          'columns' => 
          array (
            'area' => 'proxy',
            'key' => 'proxy_auth_type',
            'namespace' => 'core',
            'value' => 'BASIC',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        172 => 
        array (
          'columns' => 
          array (
            'area' => 'proxy',
            'key' => 'proxy_host',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        173 => 
        array (
          'columns' => 
          array (
            'area' => 'proxy',
            'key' => 'proxy_password',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'text-password',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        174 => 
        array (
          'columns' => 
          array (
            'area' => 'proxy',
            'key' => 'proxy_port',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        175 => 
        array (
          'columns' => 
          array (
            'area' => 'proxy',
            'key' => 'proxy_username',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        176 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'publish_default',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        177 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'rb_base_dir',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        178 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'rb_base_url',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        179 => 
        array (
          'columns' => 
          array (
            'area' => 'gateway',
            'key' => 'request_controller',
            'namespace' => 'core',
            'value' => 'index.php',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        180 => 
        array (
          'columns' => 
          array (
            'area' => 'gateway',
            'key' => 'request_method_strict',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        181 => 
        array (
          'columns' => 
          array (
            'area' => 'gateway',
            'key' => 'request_param_alias',
            'namespace' => 'core',
            'value' => 'q',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        182 => 
        array (
          'columns' => 
          array (
            'area' => 'gateway',
            'key' => 'request_param_id',
            'namespace' => 'core',
            'value' => 'id',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        183 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'resolve_hostnames',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        184 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'resource_tree_node_name',
            'namespace' => 'core',
            'value' => 'pagetitle',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        185 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'resource_tree_node_name_fallback',
            'namespace' => 'core',
            'value' => 'pagetitle',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        186 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'resource_tree_node_tooltip',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        187 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'richtext_default',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        188 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'search_default',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        189 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'send_poweredby_header',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        190 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'server_offset_time',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        191 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'server_protocol',
            'namespace' => 'core',
            'value' => 'http',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        192 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_cookie_domain',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        193 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_cookie_httponly',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        194 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_cookie_lifetime',
            'namespace' => 'core',
            'value' => '604800',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        195 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_cookie_path',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        196 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_cookie_secure',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        197 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_gc_maxlifetime',
            'namespace' => 'core',
            'value' => '604800',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        198 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_handler_class',
            'namespace' => 'core',
            'value' => 'modSessionHandler',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        199 => 
        array (
          'columns' => 
          array (
            'area' => 'session',
            'key' => 'session_name',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        200 => 
        array (
          'columns' => 
          array (
            'area' => '',
            'key' => 'settings_distro',
            'namespace' => 'core',
            'value' => 'git',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        201 => 
        array (
          'columns' => 
          array (
            'area' => '',
            'key' => 'settings_version',
            'namespace' => 'core',
            'value' => '3.0.0-dev',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        202 => 
        array (
          'columns' => 
          array (
            'area' => 'system',
            'key' => 'set_header',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        203 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'show_tv_categories_header',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        204 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'signupemail_message',
            'namespace' => 'core',
            'value' => '<p>Hello [[+uid]],</p>
        <p>Here are your login details for the [[+sname]] MODX Manager:</p>
    
        <p>
            <strong>Username:</strong> [[+uid]]<br />
            <strong>Password:</strong> [[+pwd]]<br />
        </p>
    
        <p>Once you log into the MODX Manager at [[+surl]], you can change your password.</p>
    
        <p>Regards,<br />Site Administrator</p>',
            'xtype' => 'textarea',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        205 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'site_name',
            'namespace' => 'core',
            'value' => 'Blend Site',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        206 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'site_start',
            'namespace' => 'core',
            'value' => 
            array (
              'type' => 'resource',
              'portable_value' => 
              array (
                'context' => 'web',
                'seed_key' => 'index',
              ),
              'value' => '1',
            ),
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        207 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'site_status',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        208 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'site_unavailable_message',
            'namespace' => 'core',
            'value' => 'The site is currently unavailable',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        209 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'site_unavailable_page',
            'namespace' => 'core',
            'value' => 
            array (
              'type' => 'resource',
              'portable_value' => 
              array (
                'context' => false,
                'seed_key' => false,
              ),
              'value' => '0',
            ),
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        210 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'strip_image_paths',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        211 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'symlink_merge_fields',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        212 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'syncsite_default',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        213 => 
        array (
          'columns' => 
          array (
            'area' => '',
            'key' => 'systemSettingSiteExample',
            'namespace' => 'core',
            'value' => 'Blend Site Example',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        214 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'topmenu_show_descriptions',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        215 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'tree_default_sort',
            'namespace' => 'core',
            'value' => 'menuindex',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        216 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'tree_root_id',
            'namespace' => 'core',
            'value' => 
            array (
              'type' => 'resource',
              'portable_value' => 
              array (
                'context' => false,
                'seed_key' => false,
              ),
              'value' => '0',
            ),
            'xtype' => 'numberfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        217 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'tvs_below_content',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        218 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'udperms_allowroot',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        219 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'unauthorized_page',
            'namespace' => 'core',
            'value' => 
            array (
              'type' => 'resource',
              'portable_value' => 
              array (
                'context' => 'web',
                'seed_key' => 'index',
              ),
              'value' => '1',
            ),
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        220 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'upload_files',
            'namespace' => 'core',
            'value' => 'txt,html,htm,xml,js,css,zip,gz,rar,z,tgz,tar,mp3,mp4,aac,wav,au,wmv,avi,mpg,mpeg,pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,tiff,svg,svgz,gif,psd,ico,bmp,odt,ods,odp,odb,odg,odf,md,ttf,woff,eot,scss,less,css.map',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        221 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'upload_flash',
            'namespace' => 'core',
            'value' => 'swf,fla',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        222 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'upload_images',
            'namespace' => 'core',
            'value' => 'jpg,jpeg,png,gif,psd,ico,bmp,tiff,svg,svgz',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        223 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'upload_maxsize',
            'namespace' => 'core',
            'value' => '2097152',
            'xtype' => 'textfield',
            'editedon' => '2018-03-01 19:38:58',
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        224 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'upload_media',
            'namespace' => 'core',
            'value' => 'mp3,wav,au,wmv,avi,mpg,mpeg',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        225 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'user_nav_parent',
            'namespace' => 'core',
            'value' => 'usernav',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        226 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'use_alias_path',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        227 => 
        array (
          'columns' => 
          array (
            'area' => 'file',
            'key' => 'use_browser',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        228 => 
        array (
          'columns' => 
          array (
            'area' => 'caching',
            'key' => 'use_context_resource_table',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        229 => 
        array (
          'columns' => 
          array (
            'area' => 'editor',
            'key' => 'use_editor',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        230 => 
        array (
          'columns' => 
          array (
            'area' => 'furls',
            'key' => 'use_frozen_parent_uris',
            'namespace' => 'core',
            'value' => '0',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        231 => 
        array (
          'columns' => 
          array (
            'area' => 'language',
            'key' => 'use_multibyte',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => '2018-03-01 19:38:58',
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        232 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'use_weblink_target',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        233 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'webpwdreminder_message',
            'namespace' => 'core',
            'value' => '<p>Hello [[+uid]],</p>
    
        <p>To activate your new password click the following link:</p>
    
        <p>[[+surl]]</p>
    
        <p>If successful you can use the following password to login:</p>
    
        <p><strong>Password:</strong> [[+pwd]]</p>
    
        <p>If you did not request this email then please ignore it.</p>
    
        <p>Regards,<br />
        Site Administrator</p>',
            'xtype' => 'textarea',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        234 => 
        array (
          'columns' => 
          array (
            'area' => 'authentication',
            'key' => 'websignupemail_message',
            'namespace' => 'core',
            'value' => '<p>Hello [[+uid]],</p>
    
        <p>Here are your login details for [[+sname]]:</p>
    
        <p><strong>Username:</strong> [[+uid]]<br />
        <strong>Password:</strong> [[+pwd]]</p>
    
        <p>Once you log into [[+sname]] at [[+surl]], you can change your password.</p>
    
        <p>Regards,<br />
        Site Administrator</p>',
            'xtype' => 'textarea',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        235 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'welcome_action',
            'namespace' => 'core',
            'value' => 'welcome',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        236 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'welcome_namespace',
            'namespace' => 'core',
            'value' => 'core',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        237 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'welcome_screen',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'combo-boolean',
            'editedon' => '2018-03-01 19:39:43',
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        238 => 
        array (
          'columns' => 
          array (
            'area' => 'manager',
            'key' => 'welcome_screen_url',
            'namespace' => 'core',
            'value' => '//misc.modx.com/revolution/welcome.26.html ',
            'xtype' => 'textfield',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        239 => 
        array (
          'columns' => 
          array (
            'area' => 'editor',
            'key' => 'which_editor',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'modx-combo-rte',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        240 => 
        array (
          'columns' => 
          array (
            'area' => 'editor',
            'key' => 'which_element_editor',
            'namespace' => 'core',
            'value' => '',
            'xtype' => 'modx-combo-rte',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
        241 => 
        array (
          'columns' => 
          array (
            'area' => 'site',
            'key' => 'xhtml_urls',
            'namespace' => 'core',
            'value' => '1',
            'xtype' => 'combo-boolean',
            'editedon' => NULL,
          ),
          'primaryKeyHistory' => 
          array (
          ),
          'related' => 
          array (
          ),
        ),
      ),
      'templates' => 
      array (
        1 => 'templateSiteExample',
      ),
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (isset($this->site_data['mediaSources'])) {
            $this->blender->blendManyMediaSources($this->site_data['mediaSources'], $this->getSeedsDir());
        }

        if (isset($this->site_data['contexts'])) {
            $this->blender->blendManyContexts($this->site_data['contexts'], $this->getSeedsDir());
        }

        if (isset($this->site_data['templates'])) {
            $this->blender->blendManyTemplates($this->site_data['templates'], $this->getSeedsDir());
        }

        if (isset($this->site_data['resources'])) {
            $this->blender->blendManyResources($this->site_data['resources'], $this->getSeedsDir());
        }

        if (isset($this->site_data['chunks'])) {
            $this->blender->blendManyChunks($this->site_data['chunks'], $this->getSeedsDir());
        }

        if (isset($this->site_data['plugins'])) {
            $this->blender->blendManyPlugins($this->site_data['plugins'], $this->getSeedsDir());
        }

        if (isset($this->site_data['snippets'])) {
            $this->blender->blendManySnippets($this->site_data['snippets'], $this->getSeedsDir());
        }

        if (isset($this->site_data['systemSettings'])) {
            $this->blender->blendManySystemSettings($this->site_data['systemSettings'], $this->getSeedsDir());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (isset($this->site_data['chunks'])) {
            $this->blender->revertBlendManyChunks($this->site_data['chunks'], $this->getSeedsDir());
        }

        if (isset($this->site_data['plugins'])) {
            $this->blender->revertBlendManyPlugins($this->site_data['plugins'], $this->getSeedsDir());
        }

        if (isset($this->site_data['resources']) && method_exists($this->blender, 'revertBlendManyResources')) {
            $this->blender->revertBlendManyResources($this->site_data['resources'], $this->getSeedsDir());
        }

        if (isset($this->site_data['snippets'])) {
            $this->blender->revertBlendManySnippets($this->site_data['snippets'], $this->getSeedsDir());
        }

        if (isset($this->site_data['systemSettings'])) {
            $this->blender->revertBlendManySystemSettings($this->site_data['systemSettings'], $this->getSeedsDir());
        }

        if (isset($this->site_data['templates']) && method_exists($this->blender, 'revertBlendManyResources')) {
            $this->blender->revertBlendManyTemplates($this->site_data['templates'], $this->getSeedsDir());
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = '';
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignVersion()
    {

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
    protected function assignSeedsDir()
    {
        $this->seeds_dir = 'm2018_01_10_093000_SiteExample';
    }
}
