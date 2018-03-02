<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/28/18
 * Time: 2:36 PM
 */

namespace LCI\Blend\Helpers;


class ModxConfig
{
    /** @var array  */
    protected $config = [];

    protected $config_path = '';

    /**
     * ModxConfig constructor.
     * @param string $config_path
     * @param array $config
     */
    public function __construct($config_path, array $config=[])
    {

        $this->config_path = $config_path . 'core/config/' . (defined('MODX_CONFIG_KEY') ? MODX_CONFIG_PATH : 'config'). '.inc.php';

        $defaults = [
            'site_sessionname' => 'SN' . uniqid(''),
            'core_path' => (defined('MODX_CORE_PATH') ? MODX_CORE_PATH : MODX_PATH . 'core/'),
            'mgr_path' => (defined('MODX_MANAGER_PATH') ? MODX_MANAGER_PATH : MODX_PATH . 'manager/'),
            'mgr_url' => (defined('MODX_MANAGER_URL') ? MODX_MANAGER_URL : '/manager/'),
            'connectors_path' => (defined('MODX_CONNECTORS_PATH') ? MODX_CONNECTORS_PATH : MODX_PATH . 'connectors/'),
            'connectors_url' => (defined('MODX_CONNECTORS_URL') ? MODX_CONNECTORS_URL : '/connectors/'),
            'web_path' => (defined('MODX_BASE_PATH') ? MODX_BASE_PATH : MODX_PATH),
            'web_url' => (defined('MODX_BASE_URL') ? MODX_BASE_URL : '/'),
            'processors_path' => (defined('MODX_CORE_PATH') ? MODX_CORE_PATH : MODX_PATH . 'core/') . 'model/modx/processors/',
            'assets_path' => (defined('MODX_ASSETS_PATH') ? MODX_ASSETS_PATH : MODX_PATH . 'assets/'),
            'assets_url' => (defined('MODX_ASSETS_URL') ? MODX_ASSETS_URL : '/assets/'),

            'database_dsn' => '',
            'server_dsn' => '',
            'database_type' => 'modx',
            'database_server' => 'localhost',
            'database' => 'modx',
            'database_user' => '',
            'database_password' => '',
            'database_connection_charset' => 'utf8',
            'database_charset' => 'utf8',
            'database_collation' => 'utf8_general_ci',
            'table_prefix' => 'modx_',
            'https_port' => 443,
            'http_host' => 'localhost',
            'cache_disabled' => 0,
            'inplace' => 1,
            'unpacked' => 0,
            'language' => 'en',

            'remove_setup_directory' => true,

            // @TODO ??
            'config_options' => [
                //xPDO::OPT_OVERRIDE_TABLE_TYPE => 'MyISAM'
            ],
            'driver_options' => []
        ];
        $existing = $this->load();

        $this->config = array_merge($defaults, $existing, $config);
    }

    /**
     * @param string $key
     * @param string|mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->config[$key] = $value;
        return $this;
    }

    protected function load()
    {
        // @TODO
        return [];
    }


    /**
     * Writes the config file.
     *
     * @return boolean Returns true if successful; false otherwise.
     */
    public function save() {
        $written = false;

        // needs to be versioned:
        $configTpl = dirname(__DIR__).'/database/modx/seeds/config.inc.tpl'; //  $this->blender->getSeedsDir()

        $this->config['database_password'] = addslashes($this->config['database_password']);

        $this->config['last_install_time'] = time();
        $this->config['site_id'] = uniqid('modx',true);

        /* make UUID if not set */
        if (empty($this->config['uuid'])) {
            $this->config['uuid'] = $this->generateUUID();
        }

        $this->buildDSN();

        if (file_exists($configTpl)) {
            $content = file_get_contents($configTpl);

            if ($content) {
                $replace = [];
                foreach ($this->config as $key => $value) {
                    if (is_scalar($value)) {
                        $replace['{' . $key . '}'] = $value;
                    } elseif (is_array($value)) {
                        $replace['{' . $key . '}'] = var_export($value, true);
                    }
                }

                $content = str_replace(array_keys($replace), array_values($replace), $content);

                file_put_contents($this->config_path, $content);
                chmod($this->config_path, 0600);
            }
        }

        // now the minor peices:

        return $written;
    }

    /**
     * @return void
     */
    protected function buildDSN() {
        if (array_key_exists('database_type', $this->config)) {
            $this->config['dbase'] = $this->config['database'];
            switch ($this->config['database_type']) {
                case 'sqlsrv':
                    $database_dsn = "{$this->config['database_type']}:server={$this->config['database_server']};database={$this->config['dbase']}";
                    $server_dsn = "{$this->config['database_type']}:server={$this->config['database_server']}";
                    break;
                case 'mysql':
                    $database_dsn = "{$this->config['database_type']}:host={$this->config['database_server']};dbname={$this->config['dbase']};charset={$this->config['database_connection_charset']}";
                    $server_dsn = "{$this->config['database_type']}:host={$this->config['database_server']};charset={$this->config['database_connection_charset']}";
                    break;
                default:
                    $database_dsn = '';
                    $server_dsn = '';
                    break;
            }
            $this->config['database_dsn'] = $database_dsn;
            $this->config['server_dsn'] = $server_dsn;
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
}