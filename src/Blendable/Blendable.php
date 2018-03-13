<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 3/5/18
 * Time: 11:14 AM
 */

namespace LCI\Blend\Blendable;

use LCI\Blend\Blender;

abstract class Blendable implements BlendableInterface
{
    /** @var  \modx */
    protected $modx;

    /** @var  Blender */
    protected $blender;

    /** @var string  */
    protected $opt_cache_key = '';

    /** @var array  */
    protected $cacheOptions = [];

    /** @var string */
    protected $seeds_dir = '';

    /** @var int $cache_life in seconds, 0 is forever */
    protected $cache_life = 0;

    /** @var bool  */
    protected $error = false;

    /** @var array  */
    protected $error_messages = [];

    /** @var null|\xPDOSimpleObject  */
    protected $xPDOSimpleObject = null;

    /** @var string ex: modResource */
    protected $xpdo_simple_object_class = '';

    /** @var string  */
    protected $unique_key_column = 'name';

    /** @var array ~ this should match data to be inserted via xPDO, ex [column_name => value, ...] */
    protected $blendable_xpdo_simple_object_data = [
        'name' => ''
    ];

    /** @var array ~ ['setMethodName' => 'setMethodActualName', 'setDoNotUseMethod' => false] overwrite in child classes */
    protected $load_from_array_aliases = [];

    /** @var array ~ xPDOSimpleObject->fromArray() */
    protected $current_xpdo_simple_object_data = [];

    /** @var array  */
    protected $related_data = [];

    /** @var bool  */
    protected $exists = false;

    /** @var bool  */
    protected $debug = false;

    /**
     * Element constructor.
     *
     * @param \modx $modx
     * @param Blender $blender
     */
    public function __construct(\modx $modx, Blender $blender)
    {
        $this->modx = $modx;
        $this->blender = $blender;
        if (method_exists($this, 'loadProperties') ) {
            $this->loadProperties();
        }

        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => $this->opt_cache_key,
            \xPDO::OPT_CACHE_PATH => $this->blender->getSeedsDirectory()
        ];
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     *
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeedsDir()
    {
        return $this->seeds_dir;
    }

    /**
     * @param string $dir ~ will be the directory name
     *
     * @return $this
     */
    public function setSeedsDir($dir)
    {
        $this->seeds_dir = (string) $dir;
        if (!empty($this->seeds_dir)) {
            $this->cacheOptions[\xPDO::OPT_CACHE_PATH] = $this->blender->getSeedsDirectory() . $dir . '/';
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isExists()
    {
        return $this->exists;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->error_messages;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->blendable_xpdo_simple_object_data['name'];
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->blendable_xpdo_simple_object_data['name'] = $name;
        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function loadFromArray($data=[])
    {
        foreach ($data as $column => $value) {
            $method_name = 'set'.$this->makeStudyCase($column);

            if (isset($this->load_from_array_aliases[$method_name])) {
                $method_name = $this->load_from_array_aliases[$method_name];

                if (!$method_name) {
                    continue;
                }
            }

            if (method_exists($this, $method_name) && !is_null($value)) {
                if ($this->isDebug()) {
                    $this->blender->out(__METHOD__.' call: '.$method_name.' V: '.$value);
                }
                $this->$method_name($value);

            } elseif($this->isDebug()) {
                $this->blender->out(__METHOD__.' missing: '.$method_name.' V: '.$value, true);
            }
        }
        return $this;
    }

    /**
     * @param bool $overwrite
     *
     * @return bool
     */
    public function save($overwrite=false)
    {
        $saved = false;

        $this->xPDOSimpleObject = $this->modx->getObject(
            $this->xpdo_simple_object_class,
            [
                $this->unique_key_column => $this->blendable_xpdo_simple_object_data[$this->unique_key_column]
            ]
        );
        if (is_object($this->xPDOSimpleObject)) {
            if (!$overwrite) {
                $this->error = true;
                $this->error_messages['exits'] = $this->xpdo_simple_object_class.': ' .
                    $this->blendable_xpdo_simple_object_data['name'] . ' already exists ';
                return $saved;
            }
        } else {
            $this->xPDOSimpleObject = $this->modx->newObject($this->xpdo_simple_object_class);
        }

        $this->xPDOSimpleObject->set($this->unique_key_column, $this->blendable_xpdo_simple_object_data[$this->unique_key_column]);

        foreach ($this->blendable_xpdo_simple_object_data as $column => $value) {
            $this->xPDOSimpleObject->set($column, $value);
        }

        if (method_exists($this, 'getPropertiesData')) {
            $this->xPDOSimpleObject->set('properties', $this->getPropertiesData());
        }

        $this->relatedPieces();
        if ($this->xPDOSimpleObject->save()) {
            $this->relatedPiecesAfterSave();
            if ($this->isDebug()) {
                //echo ' SAVED ';exit();
                $this->blender->out($this->getName() . ' has been installed/saved');
            }
            $saved = true;
        } else {
            if ($this->isDebug()) {
                //echo ' NO!! ';exit();
                $this->blender->out($this->getName() . ' did not install/update', true);
            }

        }

        return $saved;
    }

    /**
     * @param \xPDOSimpleObject|\xPDO\Om\xPDOSimpleObject $xPDOobject
     * @return string ~ the related seed key
     */
    public function seed($xPDOSimpleObject)
    {
        $this->xPDOSimpleObject = $xPDOSimpleObject;
        // No IDs! must get the alias and get a seed key,
        $seed_key = $this->blender->getSeedKeyFromName($this->xPDOSimpleObject->get('name'));
        $this->current_xpdo_simple_object_data = $this->xPDOSimpleObject->toArray();

        $this->seedRelated($this->xPDOSimpleObject);
        $this->current_xpdo_simple_object_data['related_data'] = $this->related_data;

        // https://docs.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.invokeevent
        $this->modx->invokeEvent(
            'OnBlendSeed',
            [
                'blender' => $this->blender,
                'blendable' => $this,
                'xPDOClass' => $this->xpdo_simple_object_class,
                'xPDOSimpleObject' => &$this->xPDOSimpleObject,
                'data' => &$this->current_xpdo_simple_object_data
            ]
        );

        // now cache it:
        $this->modx->cacheManager->set(
            $seed_key,
            $this->current_xpdo_simple_object_data,
            $this->cache_life,
            $this->cacheOptions
        );

        return $seed_key;
    }
    /**
     * @param string $name
     *
     * @return bool|\xPDOSimpleObject|\xPDO\Om\xPDOSimpleObject $xPDOobject
     */
    public function getObjectFromName($name)
    {
        return $this->modx->getObject($this->xpdo_simple_object_class, [$this->unique_key_column => $name]);
    }

    /**
     * Will load an existing modElement into element
     * @param string $name
     *
     * @return $this
     */
    public function loadObjectFromName($name)
    {
        $this->xPDOSimpleObject = $this->getObjectFromName($name);

        if (is_object($this->xPDOSimpleObject)) {
            $this->exists = true;
            $this->current_xpdo_simple_object_data = $this->xPDOSimpleObject->toArray();
            $this->loadFromArray($this->current_xpdo_simple_object_data);
            // load related data:
            $this->loadRelatedData();
        }

        return $this;
    }

    /**
     * Override in child classes
     */
    protected function loadRelatedData()
    {

    }

    /**
     * @return array
     */
    public function getArrayForCopy()
    {
        $copy = $this->current_xpdo_simple_object_data;
        $copy['related_data'] = $this->related_data;
        return $copy;
    }

    /**
     * @param string $seed_key
     * @param bool $overwrite
     *
     * @return bool
     */
    public function blendFromSeed($seed_key, $overwrite=false)
    {
        $this->loadObjectDataFromSeed($seed_key);
        return $this->blend($overwrite);
    }

    /**
     * @param bool $overwrite
     *
     * @return bool
     */
    public function blend($overwrite=false)
    {
        $save = false;
        // does it exist
        $name = $this->getName();

        $down = false;
        /** @var \LCI\Blend\Blendable\Blendable $currentVersion */
        $currentVersion = $this->loadCurrentVersion($name);
        if ($currentVersion->isExists()) {
            $this->exists = true;
            if (!$overwrite) {
                return $save;
            }
            $down = $currentVersion->getArrayForCopy();
        } else {
            $this->exists = false;
        }

        unset($this->current_xpdo_simple_object_data['id']);

        $this->modx->invokeEvent(
            'OnBlendBeforeSave',
            [
                'blender' => $this->blender,
                'blendable' => $this,
                'xPDOClass' => $this->xpdo_simple_object_class,
                'xPDOSimleObject' => &$this->xPDOSimpleObject,
                'data' => &$this->current_xpdo_simple_object_data
            ]
        );

        // load from array:
        if (count($this->current_xpdo_simple_object_data)) {
            $this->loadFromArray($this->current_xpdo_simple_object_data);
        }
        $save = $this->save($overwrite);

        if ($save) {
            // write current DB version to disk:
            $this->modx->cacheManager->set(
                'down-'.$this->getSeedKey(),
                $down,
                $this->cache_life,
                $this->cacheOptions
            );
            $this->modx->invokeEvent(
                'OnBlendAfterSave',
                [
                    'blender' => $this->blender,
                    'blendable' => $this,
                    'xPDOClass' => $this->xpdo_simple_object_class,
                    'xPDOSimleObject' => &$this->xPDOSimpleObject,
                    'data' => &$this->current_xpdo_simple_object_data
                ]
            );
        } else {
            $this->blender->out('Error did not save ', true);
        }
        return $save;
    }

    /**
     * @return string
     */
    public function getSeedKey()
    {
        return $this->blender->getSeedKeyFromName($this->getName());
    }
    /**
     * @param string $seed_key
     *
     * @return bool
     */
    public function revertBlendFromSeed($seed_key)
    {
        $this->loadObjectDataFromSeed($seed_key);
        return $this->revertBlend();
    }

    /**
     * @return bool
     */
    public function revertBlend()
    {
        $reverted = false;

        $this->xPDOSimpleObject = $this->getObjectFromName($this->getName());
        if (!is_object($this->xPDOSimpleObject)) {
            $this->xPDOSimpleObject = $this->modx->getObject($this->xpdo_simple_object_class);
        }
        // 1. get previous data from cache:
        $data = $this->modx->cacheManager->get('down-'.$this->getSeedKey(), $this->cacheOptions);

        if (!$data) {
            if ($this->isDebug()) {
                $this->blender->out('Remove old' . $this->getName());
            }
            $reverted = $this->xPDOSimpleObject->remove();

        } elseif (is_array($data)) {
            if ($this->isDebug()) {
                $this->blender->out('Restore to old ' . $this->getName());
            }
            // load old data:
            $this->xPDOSimpleObject->fromArray($data);
            $reverted = $this->xPDOSimpleObject->save();
        }

        if ($reverted || $data === false) {
            $this->revertRelatedPieces($data);
        }

        return $reverted;
    }

    /**
     * @param string $seed_key
     *
     * @return $this
     */
    protected function loadObjectDataFromSeed($seed_key)
    {
        $this->current_xpdo_simple_object_data = $this->modx->cacheManager->get($seed_key, $this->cacheOptions);
        if ($this->current_xpdo_simple_object_data == false) {
            $this->blender->out('Error: Seed could not be found: '.$seed_key.' aborting', true);
            exit();
        }
        $this->loadFromArray($this->current_xpdo_simple_object_data);
        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function makeStudyCase($name)
    {
        $StudyName = '';
        $parts = explode('_', $name);
        foreach ($parts as $part) {
            $StudyName .= ucfirst($part);
        }
        return $StudyName;
    }

    protected function relatedPieces()
    {

    }

    /**
     * @param array|bool $data ~ the data loaded from the down seed
     */
    protected function revertRelatedPieces($data)
    {

    }

    protected function relatedPiecesAfterSave()
    {

    }

    /**
     * @param \xPDOSimpleObject|\xPDO\Om\xPDOSimpleObject $xPDOSimpleObject
     *
     * @return \xPDOSimpleObject|\xPDO\Om\xPDOSimpleObject $xPDOSimpleObject
     */
    protected function seedRelated($xPDOSimpleObject)
    {
        return $xPDOSimpleObject;
    }

    /**
     * @return array
     */
    public function getRelatedData()
    {
        return $this->related_data;
    }

    /**
     * Called from loadFromArray(), for build from seeds, override in child classes
     * @param mixed $data
     *
     * @return $this
     */
    protected function setRelatedData($data)
    {
        return $this;
    }

}