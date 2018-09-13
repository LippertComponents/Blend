<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/12/18
 * Time: 12:38 PM
 */

namespace LCI\Blend\Helpers;


class Format
{
    protected $path_time_stamp;

    /**
     * Format constructor.
     * @param null $path_time_stamp
     */
    public function __construct($path_time_stamp = null)
    {
        if (!empty($path_time_stamp)) {
            $this->path_time_stamp = $path_time_stamp;
        } else {
            $this->path_time_stamp = date('Y_m_d_His');
        }
    }

    /**
     * @param string $type
     * @param string|null $name
     * @return string
     */
    public function getMigrationName($type, $name = null)
    {
        $dir_name = 'm'.$this->path_time_stamp.'_';
        if (empty($name)) {
            $name = ucfirst(strtolower($type));
            if ($name == 'Mediasource') {
                $name = 'MediaSource';
            }
        }

        $dir_name .= preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(['/', ' '], '_', $name));

        return $dir_name;
    }

    /**
     * @return false|string
     */
    public function getPathTimeStamp()
    {
        return $this->path_time_stamp;
    }

    /**
     * @param mixed|array $data
     * @param int $tabs
     *
     * @return string
     */
    public function prettyVarExport($data, $tabs = 1)
    {
        $spacing = str_repeat(' ', 4 * $tabs);

        $string = '';
        $parts = preg_split('/\R/', var_export($data, true));
        foreach ($parts as $k => $part) {
            if ($k > 0) {
                $string .= $spacing;
            }
            $string .= $part.PHP_EOL;
        }

        return trim($string);
    }

    /**
     * @param false|string $path_time_stamp
     * @return Format
     */
    public function setPathTimeStamp($path_time_stamp)
    {
        $this->path_time_stamp = $path_time_stamp;
        return $this;
    }

}