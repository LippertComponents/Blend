<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/28/18
 * Time: 11:57 AM
 */

namespace LCI\Blend\Helpers;


class SimpleCache
{
    use Files;

    protected $directory = __DIR__;

    /**
     * SimpleCache constructor.
     * @param string $directory ~ directory with cache files will live
     */
    public function __construct($directory)
    {
        $this->directory = $directory;

        if (!file_exists(rtrim($this->directory, '/'))) {
            mkdir(rtrim($this->directory, '/'), '0700', true);
        }
    }


    /**
     * @param string $key
     * @return bool|mixed
     */
    public function get($key='install-config')
    {
        $path = $this->getFullKeyPath($key);
        $data = false;

        if (file_exists($path)) {
            $data = include $path;
        }

        return $data;
    }

    /**
     * @param string $key
     * @param array $data
     */
    public function set($key='install-config', $data=[])
    {
        $content = '<?php '.PHP_EOL.
            'return ' . var_export($data, true) . ';';

        file_put_contents($this->getFullKeyPath($key), $content);
    }

    /**
     * @param null|string $key ~ if null will delete the complete directory
     */
    public function remove($key=null)
    {
        if (!empty($key)) {
            $path = $this->getFullKeyPath($key);
            if (file_exists($key)) {
                unlink($path);
            }

        } else {
            // clean the directory:
            $this->deleteDirectory(MODX_PATH . $this->directory);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getFullKeyPath($key)
    {
        return rtrim($this->directory, '/') . DIRECTORY_SEPARATOR .
            preg_replace('/[^A-Za-z0-9\_\-]/', '', str_replace(['/', ' '], '_', $key)) .
            '.php';
    }

}