<?php  return array (
  'columns' => 
  array (
    'category' => 'Parent Plugin Cat=>Child Plugin Cat',
    'description' => 'Site Example plugin test',
    'editor_type' => 0,
    'locked' => false,
    'name' => 'pluginSiteExample',
    'property_preprocess' => false,
    'properties' => 
    array (
    ),
    'source' => 'Filesystem',
    'static' => true,
    'static_file' => 'core/components/mysite/elements/plugins/pluginSiteExample.tpl',
    'content' => '$eventName = $modx->event->name; // Site Example',
    'cache_type' => 0,
    'plugincode' => '$eventName = $modx->event->name; // Site Example',
    'disabled' => false,
    'moduleguid' => '',
  ),
  'primaryKeyHistory' => 
  array (
  ),
  'related' => 
  array (
    0 => 
    array (
      'event' => 'OnUserActivate',
      'priority' => 0,
      'propertyset' => 0,
    ),
  ),
);