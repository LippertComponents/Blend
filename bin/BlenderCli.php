<?php
use LCI\Blend\Blender;
use League\CLImate\CLImate;

ini_set('display_errors', 1);

$autoloader_possible_paths = [
    // if cloned from git:
    dirname(__DIR__).'/vendor/autoload.php',
    // if installed via composer:
    dirname(dirname(dirname(__DIR__))).'/autoload.php',
];
foreach ($autoloader_possible_paths as $autoloader_path) {
    if (file_exists($autoloader_path)) {
        require_once $autoloader_path;
    }
}

/** @var string $blend_modx_migration_dir ~ Blend data migration directory */
$blend_modx_migration_dir = dirname(__DIR__).'/core/components/blend/';

/** @var string $local_config ~ path to allow you to override/set the MODX include paths */
$local_config = __DIR__.'/config.php';

if (file_exists($local_config)) {
    require_once $local_config;

} else {
    $found = false;
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
            $found = true;
            require_once $modx_path;
        }
    }
    if (!$found) {
        $climate = new CLImate;
        $climate->error('Blend could not find MODX, searched the following directories:');
        $climate->error(print_r($modx_possible_paths, true));
        exit();
    }
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $blend_modx_migration_dir = MODX_CORE_PATH.'components/blend/';
}

define('BLEND_MODX_MIGRATION_PATH', $blend_modx_migration_dir);

class BlenderCli
{
    /** @var modX */
    public $modx;

    /** @var bool  */
    protected $run = false;

    /** @var \League\CLImate\CLImate  */
    protected $climate;

    /** @var Blender  */
    protected $blend;

    /**
     * @param DECIMAL $begin_time
     */
    protected $begin_time = null;

    function __construct()
    {
        $this->begin_time = microtime(true);

        $this->modx = new modX();

        $this->modx->initialize('mgr');

        /** @var CLImate climate */
        $this->climate = new CLImate;
        $this->climate->description('Blend Data Management for MODX Revolution');

        /** @var Blender */
        $this->blend = new Blender($this->modx, ['blend_modx_migration_dir' => BLEND_MODX_MIGRATION_PATH]);
        $this->blend->setClimate($this->climate);

        $this->buildExeArgList();
        //$this->climate->arguments->parse();
    }

    /**
     *
     */
    public function run()
    {
        //$this->climate->out('Cache path: '.$this->stockpile->getCachePath());

        if ($this->climate->arguments->defined('help')) {
            $this->getUsage();

        } elseif ( $this->climate->arguments->defined('migrate') ) {
            $name = (string)$this->climate->arguments->get('name');
            $type = (string)$this->climate->arguments->get('type');
            $id = $this->climate->arguments->get('id');
            $count = $this->climate->arguments->get('count');

            if ( $this->climate->arguments->defined('generate') ) {
                $this->climate->flank('Name: '.$name);
                // create a blank migration class
                $this->blend->createBlankMigrationClassFile((string)$name, $type);

            } else {
                $method = $this->climate->arguments->get('method');
                $this->blend->runMigration($method, $type, $count, $id, $name);

            }

        } elseif ( $this->climate->arguments->defined('seeds') ) {
            // what seeds script to run?
            $name = (string)$this->climate->arguments->get('name');
            $type = $this->climate->arguments->get('type');
            $object = $this->climate->arguments->get('object');
            $id = $this->climate->arguments->get('id');
            $date = $this->climate->arguments->get('date');

            if ( $object == 'c' || $object == 'chunk' ) {
                $this->seedChunks($type, $name, $id);

            } elseif ( $object == 'p' || $object == 'plugin' ) {
                $this->seedPlugins($type, $name, $id);

            } elseif ( $object == 'r' || $object == 'resource' ) {
                $this->seedResources($type, $name, $id, $date);

            }  elseif ( $object == 's' || $object == 'snippet' ) {
                $this->seedSnippets($type, $name, $id);

            } elseif ( $object == 'x' || $object == 'systemSettings'  ) {
                $this->seedSystemSettings($type, $name, $id, $date);

            } elseif ( $object == 't' || $object == 'template'  ) {
                $this->seedTemplates($type, $name, $id);

            } elseif ( $object == 'a' || $object == 'site'  ) {
                $this->blend->makeSiteSeed($type, $name);

            }

        } elseif ( $this->climate->arguments->defined('install') ) {
            $this->blend->install();

        } elseif ( $this->climate->arguments->defined('uninstall') ) {
            $this->blend->install('down');

        } else {
            $this->getUsage();
        }

        $this->climate->out('Completed in '.(microtime(true)-$this->begin_time).' seconds')->br();
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedChunks($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modChunk');

        if (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);
            $criteria->orCondition(array(
                'name' => $id
            ));

        } else {
            $input = $this->climate->input('Enter in a comma separated list of chunk names or IDs ');
            $input->defaultTo('');
            $ids = explode(',', $input->prompt());

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'name:IN' => $ids
            ));
        }

        $this->blend->makeChunkSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedPlugins($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modPlugin');

        if (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);
            $criteria->orCondition(array(
                'name' => $id
            ));

        } else {
            $input = $this->climate->input('Enter in a comma separated list of plugin names or IDs ');
            $input->defaultTo('');
            $ids = explode(',', $input->prompt());

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'name:IN' => $ids
            ));
        }

        $this->blend->makePluginSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     * @param string $date
     */
    protected function seedResources($type, $name, $id, $date)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modResource');

        if (isset($date) && !empty($date)) {
            $date = strtotime($date);
            $criteria->where([
                'editedon:>=' => $date
            ]);
            $criteria->orCondition(array(
                'createdon:>=' => $date
            ));

        } elseif (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);

        } else {
            $input = $this->climate->input('Enter in a comma separated list of resource IDs ');
            $input->defaultTo('2');
            $resource_ids = $input->prompt();
            $ids = explode(',', $resource_ids);

            $criteria->where([
                'id:IN' => $ids
            ]);

            $include_parent_input = $this->climate->input('Would you like to include the parents? (y/n default is y) ');
            $include_parent_input->defaultTo('y');
            if (strtolower(trim($include_parent_input->prompt())) == 'y') {
                // get parents:
                $query = $this->modx->newQuery('modResource', ['id:IN' => $ids]);
                $query->select(['modResource.parent']);
                $query->prepare();
                $criteria->orCondition('`modResource`.`id` IN(' . $query->toSQL() . ')');
            }

            $include_children_input = $this->climate->input('Would you like to include direct children? (y/n default is y) ');
            $include_children_input->defaultTo('y');
            if (strtolower(trim($include_children_input->prompt())) == 'y') {
                // get direct children:
                $query = $this->modx->newQuery('modResource', ['parent:IN' => $ids]);
                $query->select(['modResource.id']);
                $query->prepare();
                $children_sql = $query->toSQL();
                $criteria->orCondition('`modResource`.`id` IN(' . $children_sql . ')');

                $include_grand_children_input = $this->climate->input('Would you like to include direct grand children? (y/n default is y) ');
                $include_grand_children_input->defaultTo('y');
                if (strtolower(trim($include_grand_children_input->prompt())) == 'y') {
                    // get grand children
                    $query = $this->modx->newQuery('modResource');
                    $query->select(['modResource.parent']);
                    $query->where('`modResource`.`id` IN('.$children_sql.')');
                    $query->prepare();
                    $criteria->orCondition('`modResource`.`id` IN('.$query->toSQL().')');
                }
            }
        }

        $this->blend->makeResourceSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedSnippets($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modSnippet');

        if (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);
            $criteria->orCondition(array(
                'name' => $id
            ));

        } else {
            $input = $this->climate->input('Enter in a comma separated list of snippet names or IDs ');
            $input->defaultTo('');
            $ids = explode(',', $input->prompt());

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'name:IN' => $ids
            ));
        }

        $this->blend->makeSnippetSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $key
     * @param string $date
     */
    protected function seedSystemSettings($type, $name, $key, $date)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modSystemSetting');

        if (isset($date) && !empty($date)) {
            $criteria->where([
                'editedon:>=' => $date
            ]);

        } elseif (!empty($key) && strlen($key) > 1) {
            $criteria->where([
                'key' => $key
            ]);

        } else {
            $input = $this->climate->input('Enter in a comma separated list of system settings');
            $input->defaultTo('');
            $names = $input->prompt();
            $criteria->where([
                'key:IN' => explode(',', $names)
            ]);
        }
        $this->blend->makeSystemSettingSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedTemplates($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modTemplate');

        if (is_numeric($id) && $id > 0) {
            $criteria->where([
                'id' => $id
            ]);

        } else {
            $input = $this->climate->input('Enter in a comma separated list of template names or IDs ');
            $input->defaultTo('');
            $ids = explode(',', $input->prompt());

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'templatename:IN' => $ids
            ));

        }

        $this->blend->makeTemplateSeeds($criteria, $type, $name);
    }

    protected function buildExeArgList()
    {
        // Help menu:
        $exe_args = [
            'install' => [
                'prefix'      => 'i',
                'longPrefix'  => 'install',
                'description' => 'Install Blend in MODX',
                'noValue'     => true,
            ],
            'migrate' => [
                'prefix'      => 'm',
                'longPrefix'  => 'migrate',
                'description' => 'Run migration, add -h for help info',
                'noValue'     => true,
            ],
            'seeds' => [
                'prefix'      => 's',
                'longPrefix'  => 'seed',
                'description' => 'Create migration seeds, add -h for help info',
                'noValue'     => true,
            ],
            'uninstall' => [
                'prefix'      => 'u',
                'longPrefix'  => 'uninstall',
                'description' => 'Uninstall Blend from MODX',
                'noValue'     => true,
            ],
            'help' => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'description' => 'Prints a usage statement',
                'noValue'     => true,
            ]
        ];

        if ($this->blend->isBlendInstalledInModx()) {
            unset($exe_args['install']);
        } else {
            unset($exe_args['uninstall']);
        }

        $this->climate->arguments->add($exe_args);
        $this->climate->arguments->parse($exe_args);

        if ( $this->climate->arguments->defined('migrate') ) {
            $this->climate->out('migrate');
            $this->climate->description('Run Blend Data migrations');
            $this->buildAllowableArgs('migrate');

        } elseif ( $this->climate->arguments->defined('seeds') ) {
            $this->climate->out('seed');
            $this->climate->description('Create Blend Data seeds');
            $this->buildAllowableArgs('seed');

        } elseif ( $this->climate->arguments->defined('install') ) {
            $this->climate->out('install');
            $this->climate->description('Install Blend Data Management for MODX Revolution');

        } else {
            $this->climate->arguments->add($exe_args);

        }


    }

    /**
     * @param string $exe
     */
    protected function buildAllowableArgs($exe='')
    {
        $this->climate = null;
        $this->climate = new CLImate();

        $command_args = [];
        // unique args:
        switch ($exe) {
            case 'migrate':
                $command_args = [
                    'migrate' => [
                        'prefix'      => 'm',
                        'longPrefix'  => 'migrate',
                        'description' => 'Run migration, add -h for help info',
                        'noValue'     => true,
                        'required'    => true
                    ],
                    'generate' => [
                        'prefix'      => 'g',
                        'longPrefix'  => 'generate',
                        'description' => 'Generate/create an empty migration class that you can build out a custom migration',
                        'noValue'     => true,
                    ],
                    'method' => [
                        'prefix'      => 'x',
                        'longPrefix'  => 'method',
                        'description' => 'Up or down(rollback), default is up',
                        'defaultValue' => 'up'
                    ],
                    'id' => [
                        'prefix'      => 'i',
                        'longPrefix'  => 'id',
                        'description' => 'ID of migration to run'
                    ],
                    'count' => [
                        'prefix'      => 'c',
                        'longPrefix'  => 'count',
                        'description' => 'How many to rollback, default is 1',
                        'defaultValue' => '1'
                    ],

                ];
                break;
            case 'seed':
                $command_args = [
                    'seeds' => [
                        'prefix'      => 's',
                        'longPrefix'  => 'seed',
                        'description' => 'Create migration seeds, add -h for help info',
                        'noValue'     => true,
                        'required'    => true
                    ],
                    'object' => [
                        'prefix'      => 'o',
                        'longPrefix'  => 'object',
                        'description' => 'Seed object, default is r, can be r(resource), c(chunk), p(plugin), s(snippet), x(systemSettings), t(template) or a(site)',
                        'default'     => 'r'
                    ],
                    'id' => [
                        'prefix'      => 'i',
                        'longPrefix'  => 'id',
                        'description' => 'ID of object to run'
                    ],
                    'date' => [
                        'prefix'      => 'd',
                        'longPrefix'  => 'date',
                        'description' => 'Date since created or modified, ex: 2018-01-31'
                    ]
                ];
                break;
        }

        // shared args:
        $command_args = $command_args + [
            'name' => [
                'prefix'      => 'n',
                'longPrefix'  => 'name',
                'description' => 'Name parameter to send with blank to name the migration file',
                'defaultValue' => null
            ],
            'type' => [
                'prefix'      => 't',
                'longPrefix'  => 'type',
                'description' => 'Server type to run migrations as, default is master. Possible master, staging, dev and local',
                'defaultValue' => 'master'
            ],
            'help' => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'description' => 'Prints a usage statement',
                'noValue'     => true,
            ]
        ];

        $this->climate->arguments->add($command_args);
        $this->climate->arguments->parse();

    }

    /**
     *
     */
    protected function getUsage()
    {
        $this->climate->usage();
    }

}

$benderCli = new BlenderCli();
$benderCli->run();
