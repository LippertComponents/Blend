<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 2:16 PM
 */

namespace LCI\Blend\Console\Modx;

use LCI\Blend\Console\BaseCommand;
use LCI\Blend\Helpers\DownloadModxVersion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\Table;

/**
 * Class Install
 * @package LCI\Blend\Console\Modx
 *
 * Class originally from the Gitify project: https://github.com/modmore/Gitify
 */
class Install extends BaseCommand
{
    public $loadConfig = false;

    public $loadMODX = false;

    protected $available_releases = [];

    /** @var array  */
    protected $install_config = [];

    protected function configure()
    {
        $this
            ->setName('modx:install')
            ->setDescription('Downloads, configures and installs a fresh MODX installation.')
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'The release version of MODX to install, in the format 2.6.1-pl. Leave empty or specify "latest" to install the last stable release.',
                'latest'
            )
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_NONE,
                'List available versions '
            )
            ->addOption(
                'branch',
                'b',
                InputOption::VALUE_OPTIONAL,
                'Install for a git branch rather then a release, ex 3.x '
            );

        // download a release:
        // 2.x download from Git: https://github.com/modxcms/revolution/archive/v2.6.0-pl.zip

        // download a branch:
        // 3.x composer update
        // https://github.com/modxcms/revolution/archive/3.x.zip
    }

    /**
     * Runs the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('list')) {
            DownloadModxVersion::showVersionsAsTable($output);

            return 1;
        }

        $version = $input->getArgument('version');


        $downloadModxVersion = new DownloadModxVersion($output);
        $install_release = true;

        if ($branch = $input->getOption('branch')) {
            $downloaded = true;
            /*
            $downloadModxVersion
                ->setDownloadDirectory(BLEND_CACHE_DIR)
                ->updateComposer();

            //    ->getGitBranch($branch);
/* */
            $install_release = false;

        } else {
            $version = $input->getArgument('version');
            if ($version == 'latest') {
                foreach (DownloadModxVersion::RELEASE_ARCHIVE as $version => $date) {
                    break;
                }
            } elseif (!isset(DownloadModxVersion::RELEASE_ARCHIVE[$version])) {
                $output->writeln('<error>Error version: '.$version.' is can not be found in Blend</error>');

                return 0;
            }

            $downloaded = $downloadModxVersion
                ->setDownloadDirectory(BLEND_CACHE_DIR)
                ->getGitRelease($version);
        }


        if ($downloaded) {
            $this->createMODXConfig();
            // finish, run migration:
            $this->modx = $this->loadBasicModx();

            // load blender:
            $this->blender = new \LCI\Blend\Blender($this->modx,
                $this->consoleUserInteractionHandler,
                [
                    // @TODO rename:
                    'blend_modx_migration_dir' => BLEND_MY_MIGRATION_PATH,
                ]
            );

            if ($install_release) {
                $this->blender->runModxInstallGitReleaseMigration($version, $this->install_config);

            } else {
                $this->blender->runModxInstallGitBranchMigration($branch, $this->install_config);

            }
            // update config?? why??

        }

        $output->writeln('Done! ' . $this->getRunStats());

        return 0;
    }

    /**
     * Asks the user to complete a bunch of details and creates a MODX CLI config xml file
     */
    protected function createMODXConfig()
    {
        // Creating config xml to install MODX with
        $this->output->writeln("Please complete following details to install MODX. Leave empty to use the [default].");

        $helper = $this->getHelper('question');

        $defaultDbHost = 'localhost';
        $question = new Question("Database Host [{$defaultDbHost}]: ", $defaultDbHost);
        $dbHost = $helper->ask($this->input, $this->output, $question);

        $defaultDbName = 'modx';
        $question = new Question("Database Name [{$defaultDbName}]: ", $defaultDbName);
        $dbName = $helper->ask($this->input, $this->output, $question);

        $question = new Question('Database User [root]: ', 'root');
        $dbUser = $helper->ask($this->input, $this->output, $question);

        $question = new Question('Database Password: ', 'root');
        $question->setHidden(true);
        $dbPass = $helper->ask($this->input, $this->output, $question);

        $question = new Question('Database Prefix [modx_]: ', 'modx_');
        $dbPrefix = $helper->ask($this->input, $this->output, $question);

        $question = new Question('Hostname [' . gethostname() . ']: ', gethostname());
        $host = $helper->ask($this->input, $this->output, $question);
        $host = rtrim(trim($host), '/');

        $defaultBaseUrl = '/';
        $question = new Question('Base URL [' . $defaultBaseUrl . ']: ', $defaultBaseUrl);
        $baseUrl = $helper->ask($this->input, $this->output, $question);
        $baseUrl = '/' . trim(trim($baseUrl), '/') . '/';
        $baseUrl = str_replace('//', '/', $baseUrl);

        $question = new Question('Manager Language [en]: ', 'en');
        $language = $helper->ask($this->input, $this->output, $question);

        $defaultMgrUser = basename(MODX_PATH) . '_admin';
        $question = new Question('Manager User [' . $defaultMgrUser . ']: ', $defaultMgrUser);
        $managerUser = $helper->ask($this->input, $this->output, $question);

        $question = new Question('Manager User Password [generated]: ', 'generate');
        $question->setHidden(true);
        $question->setValidator(function ($value) {
            if (empty($value) || strlen($value) < 8) {
                throw new \RuntimeException(
                    'Please specify a password of at least 8 characters to continue.'
                );
            }

            return $value;
        });
        $managerPass = $helper->ask($this->input, $this->output, $question);

        if ($managerPass == 'generate') {
            $managerPass = substr(str_shuffle(md5(microtime(true))), 0, rand(8, 15));
            $this->output->writeln("<info>Generated Manager Password: {$managerPass}</info>");
        }

        $question = new Question('Manager Email: ');
        $managerEmail = $helper->ask($this->input, $this->output, $question);

        $this->install_config = array(
            'database_type' => 'mysql',
            'database_server' => $dbHost,
            'database' => $dbName,
            'database_user' => $dbUser,
            'database_password' => $dbPass,
            'database_connection_charset' => 'utf8',
            'database_charset' => 'utf8',
            'database_collation' => 'utf8_general_ci',
            'table_prefix' => $dbPrefix,
            'https_port' => 443,
            'http_host' => $host,
            'cache_disabled' => 0,
            'inplace' => 1,
            'unpacked' => 0,
            'language' => $language,
            'admin_username' => $managerUser,
            'admin_password' => $managerPass,
            'admin_email' => $managerEmail,
            'core_path' => MODX_PATH . 'core/',
            'context_mgr_path' => MODX_PATH . 'manager/',
            'context_mgr_url' => $baseUrl . 'manager/',
            'context_connectors_path' => MODX_PATH . 'connectors/',
            'context_connectors_url' => $baseUrl . 'connectors/',
            'context_web_path' => MODX_PATH,
            'context_web_url' => $baseUrl,
            'remove_setup_directory' => true,

            // @TODO ??
            'config_options' => [
                //xPDO::OPT_OVERRIDE_TABLE_TYPE => 'MyISAM'
            ],
            'driver_options' => []
        );

        // create the config file:
        $this->writeConfig();
/**
        $xml = new \DOMDocument('1.0', 'utf-8');
        $modx = $xml->createElement('modx');

        foreach ($this->install_config as $key => $value) {
            $modx->appendChild($xml->createElement($key, htmlentities($value, ENT_QUOTES|ENT_XML1)));
        }

        $xml->appendChild($modx);

        $fh = fopen(MODX_PATH . 'config.xml', "w+");
        fwrite($fh, $xml->saveXML());
        fclose($fh);

        return MODX_PATH . 'config.xml';
        */
    }

    /**
     * Writes the config file.
     *
     * @return boolean Returns true if successful; false otherwise.
     */
    protected function writeConfig() {
        $written = false;

        // needs to be versioned:
        $configTpl = dirname(dirname(__DIR__)).'/database/modx/seeds/config.inc.tpl'; //  $this->blender->getSeedsDir()
        $configFile = MODX_PATH . 'core/config/' . (defined('MODX_CONFIG_KEY') ? MODX_CONFIG_PATH : 'config'). '.inc.php';
echo 'TPL: '.$configTpl.PHP_EOL.'Config: '.$configFile.PHP_EOL;
        /**
         * Sanitize MySQL Password before writing to config, escaping '
         * I'm sure there's a better way to do this, but this works for now.
         * Otherwise, we risk fatal PHP errors if the entered Password
         * contains any single quotes as they would escape the string.
         * See GitHub issue 12502 for more information. https://github.com/modxcms/revolution/issues/12502
         */
        $this->install_config['database_password'] = addslashes($this->install_config['database_password']);

        $this->install_config['last_install_time'] = time();
        $this->install_config['site_id'] = uniqid('modx',true);

        /* make UUID if not set */
        if (empty($this->install_config['uuid'])) {
            $this->install_config['uuid'] = $this->generateUUID();
        }

        $this->buildDSN();

        if (file_exists($configTpl)) {
            echo 'TPL has been loaded'.PHP_EOL;
            $content = file_get_contents($configTpl);

            if ($content) {
                $replace = [];
                foreach ($this->install_config as $key => $value) {
                    if (is_scalar($value)) {
                        $replace['{' . $key . '}'] = "{$value}";
                    } elseif (is_array($value)) {
                        $replace['{' . $key . '}'] = var_export($value, true);
                    }
                }

                $content = str_replace(array_keys($replace), array_values($replace), $content);

                if ($configHandle = @ fopen($configFile, 'wb')) {
                    $written = @ fwrite($configHandle, $content);
                    @ fclose($configHandle);
                }
            }
        }
        $perms = $this->getUserInstallConfigValue('new_file_permissions', sprintf("%04o", 0666 & (0777 - umask())));

        if (is_string($perms)) {
            $perms = octdec($perms);
        }

        $chmodSuccess = @ chmod($configFile, $perms);
        /**
        if ($written) {
            $this->addResult(modInstallRunner::RESULT_SUCCESS,'<p class="ok">'.$this->install->lexicon('config_file_written').'</p>');
        } else {
            $this->addResult(modInstallRunner::RESULT_FAILURE,'<p class="notok">'.$this->install->lexicon('config_file_err_w').'</p>');
        }
        if ($chmodSuccess) {
            $this->addResult(modInstallRunner::RESULT_SUCCESS,'<p class="ok">'.$this->install->lexicon('config_file_perms_set').'</p>');
        } else {
            $this->addResult(modInstallRunner::RESULT_WARNING,'<p>'.$this->install->lexicon('config_file_perms_notset').'</p>');
        }

         */
        return $written;
    }

    /**
     * @return void
     */
    protected function buildDSN() {
        if (array_key_exists('database_type', $this->install_config)) {
            $this->install_config['dbase'] = $this->install_config['database'];
            switch ($this->install_config['database_type']) {
                case 'sqlsrv':
                    $database_dsn = "{$this->install_config['database_type']}:server={$this->install_config['database_server']};database={$this->install_config['dbase']}";
                    $server_dsn = "{$this->install_config['database_type']}:server={$this->install_config['database_server']}";
                    break;
                case 'mysql':
                    $database_dsn = "{$this->install_config['database_type']}:host={$this->install_config['database_server']};dbname={$this->install_config['dbase']};charset={$this->install_config['database_connection_charset']}";
                    $server_dsn = "{$this->install_config['database_type']}:host={$this->install_config['database_server']};charset={$this->install_config['database_connection_charset']}";
                    break;
                default:
                    $database_dsn = '';
                    $server_dsn = '';
                    break;
            }
            $this->install_config['database_dsn'] = $database_dsn;
            $this->install_config['server_dsn'] = $server_dsn;
        }
    }

    /**
     * Generates a random universal unique ID for identifying modx installs
     *
     * @return string A universally unique ID
     */
    protected function generateUUID() {
        srand(intval(microtime(true) * 1000));
        $b = md5(uniqid(rand(),true),true);
        $b[6] = chr((ord($b[6]) & 0x0F) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3F) | 0x80);
        return implode('-',unpack('H8a/H4b/H4c/H4d/H12e',$b));
    }


    /**
     * @param array|string $message
     */
    protected function addResultMessage($message)
    {
        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => 'modx'
        ];

        $this->results[] = $message;
        // now cache it:
        $this->modx->cacheManager->set(
            $this->blender->getSeedsDir().'-'.$this->method,
            $this->results,
            0,
            $this->cacheOptions
        );
    }

    /**
     * @param string $key
     * @param mixed
     * @return bool|mixed
     */
    protected function getUserInstallConfigValue($key, $default=false)
    {
        if (isset($this->install_config[$key])) {
            return $this->install_config[$key];
        }

        return $default;
    }

    protected function loadBasicModx()
    {
        $modx = null;

        /* to validate installation, instantiate the modX class and run a few tests */
        if (include_once (MODX_PATH . 'core/model/modx/modx.class.php')) {
            $modx = new \modX(MODX_PATH . 'core/config/', array(
                \xPDO::OPT_SETUP => true,
            ));
            if (!is_object($modx) || !($modx instanceof modX)) {
                //$errors[] = '<p>'.$this->lexicon('modx_err_instantiate').'</p>';
            } else {
                $modx->setLogTarget(array(
                    'target' => 'FILE',
                    'options' => array(
                        'filename' => 'install.' . (defined('MODX_CONFIG_KEY') ? MODX_CONFIG_PATH : '') . '.' . strftime('%Y%m%dT%H%M%S') . '.log'
                    )
                ));

                /* try to initialize the mgr context */
                $modx->initialize('mgr');
                if (!$modx->isInitialized()) {
                    $errors[] = '<p>'.$this->lexicon('modx_err_instantiate_mgr').'</p>';
                }
            }
        } else {
            $errors[] = '<p>'.$this->lexicon('modx_class_err_nf').'</p>';
        }

        return $modx;
    }

}
