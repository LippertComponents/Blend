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

        $this->climate = new CLImate;

        $this->climate->description('Blend Data Management for MODX Revolution');
        $this->buildAllowableArgs();
        //$this->climate->arguments->parse();

        /** @var Blender */
        $this->blend = new Blender($this->modx, ['blend_modx_migration_dir' => BLEND_MODX_MIGRATION_PATH]);
        $this->blend->setClimate($this->climate);
    }

    /**
     *
     */
    public function run()
    {
        //$this->climate->out('Cache path: '.$this->stockpile->getCachePath());
        $name = (string)$this->climate->arguments->get('name');
        $type = (string)$this->climate->arguments->get('type');

        if ( $this->climate->arguments->defined('migrate') ) {
            $method = $this->climate->arguments->get('method');
            $this->blend->runMigration($method, $type);


        }  elseif ( $this->climate->arguments->defined('blank') ) {
            // create a blank migration class
            $this->blend->createBlankMigrationClassFile((string)$name, $type);


        } elseif ( $this->climate->arguments->defined('resource') ) {

            $id = $this->climate->arguments->get('resource');
            if (empty($id) || !is_numeric($id)) {

                $input = $this->climate->input('Enter in a comma separated list of resource IDs, will get children as well ');
                $input->defaultTo('2');
                $resource_ids = $input->prompt();
                $ids = explode(',', $resource_ids);
                foreach ($ids as $id) {
                    $this->blend->makeResourceSeedsFromParent($id, true, $type, $name);
                }

            } else {

            }

        }  elseif ( $this->climate->arguments->defined('templates') ) {
            $id = $this->climate->arguments->get('templates');
            if (is_numeric($id) && $id > 0) {
                $templates = $id;
            } else {
                $input = $this->climate->input('Enter in a comma separated list of template names or IDs ');
                $input->defaultTo('');
                $templates = $input->prompt();
            }

            $this->blend->makeTemplateSeeds(explode(',', $templates), $type, $name);

        } elseif ( $this->climate->arguments->defined('install') ) {
            $method = $this->climate->arguments->get('method');
            $this->blend->install($method);

        } else {
            $this->getUsage();
        }

        $this->climate->out('Completed in '.(microtime(true)-$this->begin_time).' seconds')->br();
    }

    /**
     *
     */
    protected function buildAllowableArgs()
    {
        // Help menu:
        $this->climate->arguments->add([
            /*
            'all' => [
                'prefix'      => 'a',
                'longPrefix'  => 'all',
                'description' => '(re)Cache all resources',
                'noValue'     => true,
            ],*/
            'migrate' => [
                'prefix'      => 'm',
                'longPrefix'  => 'migrate',
                'description' => 'Run migration',
                'noValue'     => true,
            ],
            'blank' => [
                'prefix'      => 'b',
                'longPrefix'  => 'blank',
                'description' => 'Create a blank migration class',
                'noValue'     => true,
            ],
            'name' => [
                'prefix'      => 'n',
                'longPrefix'  => 'name',
                'description' => 'Name parameter to send with blank to name the migration file',
                'defaultValue' => null
            ],
            'method' => [
                'prefix'      => 'x',
                'longPrefix'  => 'method',
                'description' => 'Up or down, default is up',
                'defaultValue' => 'up'
            ],
            'type' => [
                'prefix'      => 't',
                'longPrefix'  => 'type',
                'description' => 'Server type to run migrations as, default is master. Possible master, staging, dev and local',
                'defaultValue' => 'master'
            ],
            'resource' => [
                'prefix'      => 'r',
                'longPrefix'  => 'resource',
                'description' => 'Seed resources',
            ],
            'templates' => [
                'prefix'      => 't',
                'longPrefix'  => 'templates',
                'description' => 'Seed templates and TVs',
            ],
            'install' => [
                'prefix'      => 'i',
                'longPrefix'  => 'install',
                'description' => 'Install Stockpile in MODX',
                'noValue'     => true,
            ],
            'help' => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'description' => 'Prints a usage statement',
                'noValue'     => true,
            ]
        ]);

        $this->climate->arguments->parse();
        if ( $this->climate->arguments->get('promote')) {
            $this->run = true;
        }
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