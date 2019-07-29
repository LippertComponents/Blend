<?php


namespace LCI\Blend\Transport;


use LCI\MODX\Console\Console;

class MODXPackagesConfig
{
    /** @var \modX */
    public static $modx;

    /** @var bool|Console */
    protected static $console = false;

    /** @var string */
    protected static $package_file;

    /** @var bool|array ~ ['package_name' => ['signature' => 'ace-1.8.0-pl', 'provider' => 'modx.com', 'latest' => true], ... ] */
    protected static $packages = false;

    /**
     * @param string $signature - ex: ace-1.8.0-pl
     * @param bool $latest_version
     * @param string $provider_name
     */
    public static function addPackageConfig($signature, $latest_version=true, $provider_name='modx.com')
    {
        static::loadMODXTransportPackageInfo();
        $package_parts = MODXPackages::getVersionInfo($signature);

        static::$packages[$package_parts['base']] = [
            'signature' => $signature,
            'latest' => $latest_version,
            'provider' => $provider_name
        ];

        ksort(static::$packages);

        static::writeCacheFile(static::$package_file, static::$packages);
    }

    /**
     * @return array|bool
     */
    public static function getPackages()
    {
        static::loadMODXTransportPackageInfo();

        return static::$packages;
    }

    /**
     * @param string $signature
     */
    public static function removePackageConfig($signature)
    {
        static::loadMODXTransportPackageInfo();
        $package_parts = MODXPackages::getVersionInfo($signature);

        if (isset(static::$packages[$package_parts['base']])) {
            unset(static::$packages[$package_parts['base']]);

            static::writeCacheFile(static::$package_file, static::$packages);
        }
    }

    /**
     * @return \LCI\MODX\Console\Console
     */
    protected static function getConsole()
    {
        if (!static::$console) {
            /** @var \LCI\MODX\Console\Console $console */
            static::$console = new Console();
        }

        return static::$console;
    }

    /**
     *
     */
    protected static function loadMODXTransportPackageInfo()
    {
        /** @var \LCI\MODX\Console\Console $console */
        $console = static::getConsole();

        if (empty(static::$modx)) {
            static::$modx = $console->loadMODX();
        }

        if (!static::$packages) {
            static::$packages = [];

            static::$package_file = $console->getConfigFilePaths()['config_dir'] . 'lci_modx_transport_package.php';

            if (file_exists(static::$package_file)) {
                static::$packages = include static::$package_file;
            }
        }
    }

    /**
     * @param string $file
     * @param array $data
     */
    protected static function writeCacheFile($file, $data)
    {
        $content = '<?php ' . PHP_EOL .
            'return ' . var_export($data, true) . ';';

        file_put_contents($file, $content);
    }
}
