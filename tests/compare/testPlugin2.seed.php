<?php  return array (
  'columns' =>
  array (
    'source' => 'Filesystem',
    'property_preprocess' => false,
    'name' => 'testPlugin2',
    'description' => 'This is my 2nd test plugin, note this is limited to 255 or something and no HTML',
    'editor_type' => 0,
    'category' => '',
    'cache_type' => 0,
    'plugincode' => '$eventName = $modx->event->name;//2nd',
    'locked' => false,
    'properties' => NULL,
    'disabled' => false,
    'moduleguid' => '',
    'static' => false,
    'static_file' => '',
    'content' => '$eventName = $modx->event->name;//2nd',
  ),
  'primaryKeyHistory' =>
    array (
    ),
  'related' =>
  array (
    0 => 
    array (
      'event' => 'OnWebPageInit',
      'priority' => 0,
      'propertyset' => 0,
    ),
  ),
);
