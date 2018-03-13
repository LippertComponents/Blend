<?php  return array (
  'name' => 'testMediaSource1',
  'description' => 'This is my test media source, note this is limited to 255 or something and no HTML',
  'class_key' => 'sources.modFileMediaSource',
  'properties' => 
  array (
    'basePath' => 
    array (
      'name' => 'basePath',
      'desc' => 'prop_file.basePath_desc',
      'type' => 'textfield',
      'options' => '',
      'value' => '',
      'lexicon' => 'core:source',
    ),
    'basePathRelative' => 
    array (
      'name' => 'basePathRelative',
      'desc' => 'prop_file.basePathRelative_desc',
      'type' => 'combo-boolean',
      'options' => '',
      'value' => true,
      'lexicon' => 'core:source',
    ),
    'baseUrl' => 
    array (
      'name' => 'baseUrl',
      'desc' => 'prop_file.baseUrl_desc',
      'type' => 'textfield',
      'options' => '',
      'value' => '',
      'lexicon' => 'core:source',
    ),
    'baseUrlRelative' => 
    array (
      'name' => 'baseUrlRelative',
      'desc' => 'prop_file.baseUrlRelative_desc',
      'type' => 'combo-boolean',
      'options' => '',
      'value' => true,
      'lexicon' => 'core:source',
    ),
    'allowedFileTypes' => 
    array (
      'name' => 'allowedFileTypes',
      'desc' => 'prop_file.allowedFileTypes_desc',
      'type' => 'textfield',
      'options' => '',
      'value' => '',
      'lexicon' => 'core:source',
    ),
    'imageExtensions' => 
    array (
      'name' => 'imageExtensions',
      'desc' => 'prop_file.imageExtensions_desc',
      'type' => 'textfield',
      'value' => 'jpg,jpeg,png,gif,svg',
      'lexicon' => 'core:source',
    ),
    'thumbnailType' => 
    array (
      'name' => 'thumbnailType',
      'desc' => 'prop_file.thumbnailType_desc',
      'type' => 'list',
      'options' => 
      array (
        0 => 
        array (
          'name' => 'PNG',
          'value' => 'png',
        ),
        1 => 
        array (
          'name' => 'JPG',
          'value' => 'jpg',
        ),
        2 => 
        array (
          'name' => 'GIF',
          'value' => 'gif',
        ),
      ),
      'value' => 'png',
      'lexicon' => 'core:source',
    ),
    'thumbnailQuality' => 
    array (
      'name' => 'thumbnailQuality',
      'desc' => 'prop_s3.thumbnailQuality_desc',
      'type' => 'textfield',
      'options' => '',
      'value' => 90,
      'lexicon' => 'core:source',
    ),
    'visibility' => 
    array (
      'name' => 'visibility',
      'desc' => 'prop_file.visibility_desc',
      'type' => 'textfield',
      'options' => '',
      'value' => 'public',
      'lexicon' => 'core:source',
    ),
    'skipFiles' => 
    array (
      'name' => 'skipFiles',
      'desc' => 'prop_file.skipFiles_desc',
      'type' => 'textfield',
      'options' => '',
      'value' => '.svn,.git,_notes,nbproject,.idea,.DS_Store',
      'lexicon' => 'core:source',
    ),
  ),
  'is_stream' => true,
  'related_data' => 
  array (
  ),
);