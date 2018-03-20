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

    /** @var string ~ blend or revert */
    protected $type = 'blend';

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

    /** @var array set when a change of name/alias has been done */
    protected $unique_key_history = [];

    /** @var array ~ this should match data to be inserted via xPDO, ex [column_name => value, ...] */
    protected $blendable_xpdo_simple_object_data = [
        'name' => ''
    ];

    /** @var array ~ ['setMethodName' => 'setMethodActualName', 'setDoNotUseMethod' => false] overwrite in child classes */
    protected $load_from_array_aliases = [];

    /** @var array ~ list any fields/columns to be ignored on making seeds, like id */
    protected $ignore_seed_fields = ['id'];

    /** @var array ~ xPDOSimpleObject->fromArray() */
    protected $current_xpdo_simple_object_data = [];

    /** @var array  */
    protected $related_data = [];

    /** @var bool  */
    protected $exists = false;

    /** @var bool  */
    protected $debug = false;

    /**
     * Blendable constructor.
     *
     * @param \modx $modx
     * @param Blender $blender
     * @param string|array $unique_value
     */
    public function __construct(\modx $modx, Blender $blender, $unique_value='')
    {
        $this->modx = $modx;
        $this->blender = $blender;
        if (method_exists($this, 'loadProperties') ) {
            $this->loadProperties();
        }

        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => $this->opt_cache_key,
            \xPDO::OPT_CACHE_PATH => $this->blender->getSeedsPath()
        ];

        $this->setUniqueCriteria($unique_value);
        if (!empty($unique_value)) {
            $this->loadObject();
        }
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
            $this->cacheOptions[\xPDO::OPT_CACHE_PATH] = $this->blender->getSeedsPath() . $dir . '/';
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
     * @param string $type ~ seed or revert
     * @return string
     */
    public function getSeedKey($type='seed')
    {
        $name = $this->blendable_xpdo_simple_object_data[$this->unique_key_column];
        if (method_exists($this, 'getFieldName')) {
            $name = $this->getFieldName();
        }
        $key = $this->blender->getSeedKeyFromName($name);

        switch ($type) {
            case 'revert':
                $seed_key = 'revert-' . $key;
                break;

            case 'seed':
                // no break
            default:
                $seed_key = $key;
        }

        return $seed_key;
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
        if ($this->type == 'blend') {
            /** @var \LCI\Blend\Blendable\Blendable $currentVersion */
            $currentVersion = $this->getCurrentVersion();
            $currentVersion
                ->setRelatedData($this->related_data)
                ->seed('revert');
        }

        $this->modx->invokeEvent(
            'OnBlendBeforeSave',
            [
                'blender' => $this->blender,
                'blendable' => $this,
                'data' => &$this->blendable_xpdo_simple_object_data,
                'type' => $this->type,
                'xPDOClass' => $this->xpdo_simple_object_class,
                'xPDOSimleObject' => &$this->xPDOSimpleObject
            ]
        );

        $save = $this->save($overwrite);

        if ($save) {
            $this->modx->invokeEvent(
                'OnBlendAfterSave',
                [
                    'blender' => $this->blender,
                    'blendable' => $this,
                    'data' => &$this->blendable_xpdo_simple_object_data,
                    'type' => $this->type,
                    'xPDOClass' => $this->xpdo_simple_object_class,
                    'xPDOSimleObject' => &$this->xPDOSimpleObject
                ]
            );
        }

        return $save;
    }

    /**
     * @param bool $make_revert_seed
     * @return bool
     */
    public function delete($make_revert_seed=true)
    {
        if ($make_revert_seed) {
            $this->seed('revert');
        }
        $removed = false;
        if ($this->xPDOSimpleObject->remove()) {

            $this->onDeleteRevertRelatedPieces();
            if ($this->isDebug()) {
                $this->blender->out($this->blendable_xpdo_simple_object_data[$this->unique_key_column] . ' has been removed/deleted');
            }
            $removed = true;

        } else {
            if ($this->isDebug()) {
                $this->blender->out($this->blendable_xpdo_simple_object_data[$this->unique_key_column] . ' did not remove/delete', true);
            }
        }

        return $removed;
    }

    /**
     * @return bool
     */
    public function revertBlend()
    {
        $seed_key = $this->getSeedKey('revert');
        $this->type = 'revert';
        if (!$this->loadObjectDataFromSeed($seed_key) || !$this->blendable_xpdo_simple_object_data) {
            return $this->delete(false);
        }

        return $this->blend(true);
    }

    /**
     * @param string $type ~ seed or revert
     * @return string ~ the related seed key
     */
    public function seed($type='seed')
    {
        $data = false;
        // No IDs! must get the alias and get a seed key,
        $seed_key = $this->getSeedKey($type);

        if (is_object($this->xPDOSimpleObject)) {
            $this->current_xpdo_simple_object_data = $this->xPDOSimpleObject->toArray();

            foreach ($this->current_xpdo_simple_object_data as $column => $value) {
                if (in_array($column, $this->ignore_seed_fields)) {
                    continue;
                }
                // Any child class can create a seed method, an example for modResource:
                // seedTemplate(1) and would return the string name
                $method = 'seed' . $this->makeStudyCase($column);
                if (method_exists($this, $method)) {
                    $value = $this->$method($value);
                }
                $this->blendable_xpdo_simple_object_data[$column] = $value;
            }

            $this->seedRelated($type);

            $data = [
                'columns' => $this->blendable_xpdo_simple_object_data,
                'primaryKeyHistory' => $this->unique_key_history,
                'related' => $this->related_data
            ];

        } elseif ($type == 'revert') {

            $this->seedRelated($type);

            $data = [
                'columns' => false,
                'primaryKeyHistory' => [],
                'related' => $this->related_data
            ];

            if ($this->isDebug()) {
                $this->blender->out('Data not found to make seed: '.$seed_key);
            }

        } elseif ($type == 'seed') {
            if ($this->isDebug()) {
                $this->blender->out('Data not found to make seed: '.$seed_key);
            }
        }

        // https://docs.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.invokeevent
        $this->modx->invokeEvent(
            'OnBlendSeed',
            [
                'blender' => $this->blender,
                'blendable' => $this,
                'type' => $type,
                'xPDOClass' => $this->xpdo_simple_object_class,
                'xPDOSimpleObject' => &$this->xPDOSimpleObject,
                'data' => &$data
            ]
        );

        // now cache it:
        $this->modx->cacheManager->set(
            $seed_key,
            $data,
            $this->cache_life,
            $this->cacheOptions
        );

        return $seed_key;
    }

    /**
     * @param bool $overwrite
     *
     * @return bool
     */
    protected function save($overwrite=false)
    {
        $saved = false;

        if (is_object($this->xPDOSimpleObject)) {
            if (!$overwrite) {
                $this->error = true;
                $this->error_messages['exits'] = $this->xpdo_simple_object_class.': ' .
                    $this->blendable_xpdo_simple_object_data[$this->unique_key_column] . ' already exists ';
                return $saved;
            }
        } else {
            $this->xPDOSimpleObject = $this->modx->newObject($this->xpdo_simple_object_class);
        }

        $this->xPDOSimpleObject->set($this->unique_key_column, $this->blendable_xpdo_simple_object_data[$this->unique_key_column]);

        foreach ($this->blendable_xpdo_simple_object_data as $column => $value) {
            // Any child class can create a convert method, an example for modResource:
            // convertTemplate('String name') and would return the numeric ID
            $method = 'convert'.$this->makeStudyCase($column);
            if (method_exists($this, $method)) {
                $value = $this->$method($value);
            }
            $this->xPDOSimpleObject->set($column, $value);
        }

        if (method_exists($this, 'getPropertiesData')) {
            $this->xPDOSimpleObject->set('properties', $this->getPropertiesData());
        }

        $this->attachRelatedPieces();

        if ($this->xPDOSimpleObject->save()) {
            $this->attachRelatedPiecesAfterSave();
            if ($this->isDebug()) {
                $this->blender->out($this->getName() . ' has been installed/saved');
            }
            $saved = true;

        } else {
            if ($this->isDebug()) {
                $this->blender->out($this->getName() . ' did not install/update', true);
            }

        }

        return $saved;
    }

    protected function getUniqueCriteria()
    {
        return [
            $this->unique_key_column => $this->blendable_xpdo_simple_object_data[$this->unique_key_column]
        ];
    }

    /**
     * @param string|array $criteria
     */
    protected function setUniqueCriteria($criteria)
    {
        $this->blendable_xpdo_simple_object_data[$this->unique_key_column] = $criteria;
    }

    /**
     * Will load an existing xPDOSimpleObject if it exists, child class needs to class this one
     * @return $this
     */
    protected function loadObject()
    {
        $this->xPDOSimpleObject = $this->modx->getObject($this->xpdo_simple_object_class, $this->getUniqueCriteria());

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
     * @param array $data ~ convert the db data object to blend portable data
     *
     * @return $this
     */
    protected function loadFromArray($data=[])
    {
        foreach ($data as $column => $value) {
            $method_name = 'seed'.$this->makeStudyCase($column);

            if (method_exists($this, $method_name) && !is_null($value)) {
                if ($this->isDebug()) {
                    $this->blender->out(__METHOD__.' call: '.$method_name.' V: '.$value);
                }
                $value = $this->$method_name($value);
            }

            $method_name = 'setField'.$this->makeStudyCase($column);

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
     * Override in child classes
     */
    protected function loadRelatedData()
    {

    }

    /**
     * @param string $seed_key
     *
     * @return bool|array
     */
    protected function loadObjectDataFromSeed($seed_key)
    {
        $data = $this->modx->cacheManager->get($seed_key, $this->cacheOptions);
        if ($data == false) {
            if ($this->type == 'blend') {
                $this->blender->out('Error: Seed could not be found: '.$seed_key.' aborting', true);
                exit();
            }

        } else {
            $this->blendable_xpdo_simple_object_data = $data['columns'];
            $this->unique_key_history = $data['primaryKeyHistory'];
            $this->related_data = $data['related'];
        }

        return $data;
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

    /**
     * This method is called just before blend/save()
     */
    protected function attachRelatedPieces()
    {

    }

    /**
     * This method is called just after a successful blend/save()
     */
    protected function attachRelatedPiecesAfterSave()
    {

    }

    /**
     *
     */
    protected function onDeleteRevertRelatedPieces()
    {

    }

    /**
     * @var string $type blend or revert
     */
    protected function seedRelated($type='blend')
    {
        // load related data:
        $this->loadRelatedData();

    }

    /**
     * @return array
     */
    public function getRelatedData()
    {
        return $this->related_data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setRelatedData($data)
    {
        $this->related_data = $data;
        return $this;
    }

}