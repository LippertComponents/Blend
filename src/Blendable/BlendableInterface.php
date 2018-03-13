<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 3/5/18
 * Time: 11:13 AM
 */

namespace LCI\Blend\Blendable;

use LCI\Blend\Blender;

interface BlendableInterface
{

    /**
     * Element constructor.
     *
     * @param \modx $modx
     * @param Blender $blender
     */
    public function __construct(\modx $modx, Blender $blender);

    /**
     * @return bool
     */
    public function isDebug();

    /**
     * @param bool $debug
     *
     * @return $this
     */
    public function setDebug($debug);

    /**
     * @return string
     */
    public function getSeedsDir();

    /**
     * @param string $dir ~ will be the directory name
     *
     * @return $this
     */
    public function setSeedsDir($dir);

    /**
     * Does the DB object exist
     * @return bool
     */
    public function isExists();

    /**
     * @param string $seed_key
     * @param bool $overwrite ~ overwrite existing data object
     *
     * @return bool
     */
    public function blendFromSeed($seed_key, $overwrite=false);

    /**
     * @param bool $overwrite ~ overwrite existing data object
     *
     * @return bool
     */
    public function blend($overwrite=false);

    /**
     * @param string $name
     *
     * @return $this ~ new self()
     */
    public function loadCurrentVersion($name);

    /**
     * @param array $data
     *
     * @return $this
     */
    public function loadFromArray($data=[]);

    /**
     * @param string $seed_key
     *
     * @return bool
     */
    public function revertBlendFromSeed($seed_key);

    /**
     * @return bool
     */
    public function revertBlend();

    /**
     * @param \xPDOSimpleObject|\xPDO\Om\xPDOSimpleObject $xPDOobject
     * @return string ~ the related seed key
     */
    public function seed($xPDOSimpleObject);

}