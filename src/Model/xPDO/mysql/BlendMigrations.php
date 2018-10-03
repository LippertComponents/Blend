<?php
namespace LCI\Blend\Model\xPDO\mysql;

use xPDO\xPDO;

class BlendMigrations extends \LCI\Blend\Model\xPDO\BlendMigrations
{

    public static $metaMap = array (
        'package' => 'LCI\\Blend\\Model\\xPDO',
        'version' => '3.0',
        'table' => 'blend_migrations',
        'extends' => 'xPDO\\Om\\xPDOSimpleObject',
        'fields' => 
        array (
            'project' => 'local',
            'name' => NULL,
            'version' => NULL,
            'type' => 'master',
            'description' => NULL,
            'status' => 'ready',
            'author' => NULL,
            'created_at' => 'CURRENT_TIMESTAMP',
            'processed_at' => NULL,
            'ran_sequence' => NULL,
        ),
        'fieldMeta' => 
        array (
            'project' =>
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => 'local',
            ),
            'name' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
            ),
            'version' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '32',
                'phptype' => 'string',
                'null' => true,
            ),
            'type' => 
            array (
                'dbtype' => 'set',
                'precision' => '\'master\',\'stagging\',\'dev\',\'local\'',
                'phptype' => 'string',
                'null' => false,
                'default' => 'master',
            ),
            'description' => 
            array (
                'dbtype' => 'text',
                'phptype' => 'string',
                'null' => true,
            ),
            'status' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '16',
                'phptype' => 'string',
                'null' => false,
                'default' => 'ready',
            ),
            'author' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => true,
            ),
            'created_at' => 
            array (
                'dbtype' => 'timestamp',
                'phptype' => 'timestamp',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'processed_at' => 
            array (
                'dbtype' => 'timestamp',
                'phptype' => 'timestamp',
                'null' => true,
            ),
            'ran_sequence' =>
            array(
                'dbtype' => 'int',
                'phptype' => 'int',
                'null' => true,
            ),
        ),
    );
}
