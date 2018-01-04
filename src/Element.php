<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 11:47 AM
 */

namespace LCI\Blend;


use function PHPSTORM_META\type;

abstract class Element
{
    /** @var  \modx */
    protected $modx;

    /** @var  Blender */
    protected $blender;

    /** @var array  */
    protected $cacheOptions = [];

    /** @var string */
    protected $timestamp = '';

    /** @var int $cache_life in seconds, 0 is forever */
    protected $cache_life = 0;

    /** @var string ~ xPDOObject class name, example: modChunk */
    protected $element_class;

    /** @var string  */
    protected $name_column_name = 'name';

    /** @var  string */
    protected $name;

    /** @var  string */
    protected $description;

    /** @var  int */
    protected $media_source_id;

    /** @var  bool */
    protected $static = null;

    /** @var string  */
    protected $code = null;

    /** @var  string */
    protected $static_file;

    /** @var null|Properties  */
    protected $properties = null;

    /** @var bool  */
    protected $error = false;

    /** @var array  */
    protected $error_messages = [];

    /** @var array  */
    protected $category_names = [];

    /** @var null|\xPDOObject  */
    protected $element = null;

    /** @var array  */
    protected $element_data = [];

    /** @var bool  */
    protected $exists = false;

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
        $this->properties = new Properties();
        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => 'elements',
            \xPDO::OPT_CACHE_PATH => $this->blender->getSeedsDirectory()
        ];
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->setElementClass('');
        return $this;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp ~ will be the directory name
     *
     * @return $this
     */
    public function setSeedTimeDir($timestamp)
    {
        $this->timestamp = (string) $timestamp;
        if (!empty($this->timestamp)) {
            $this->cacheOptions[\xPDO::OPT_CACHE_PATH] = $this->blender->getSeedsDirectory() . $timestamp . '/';
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->error;
    }
    /**
     * @return bool
     */
    public function isExists()
    {
        return $this->exists;
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->error_messages;
    }

    /**
     * @return mixed
     */
    public function getElementClass()
    {
        return $this->element_class;
    }

    /**
     * @param string $element_class ~ modChunk, modSnippet, modPlugin, modTemplate, modTemplateVariable
     *
     * @return $this
     */
    public function setElementClass($element_class)
    {
        switch ($element_class) {
            case 'modChunk':
                break;
            case 'modSnippet':
                break;
            case 'modPlugin':
                break;
            case 'modTemplate':
                $this->name_column_name = 'templatename';
                break;
            case 'modTemplateVar':
                // no break
            case 'modTemplateVariable':
                $element_class = 'modTemplateVar';
                break;
            default:
                $this->error = true;
                $this->error_messages['type'] = 'Invalid element type set';
        }
        $this->element_class = $element_class;
        return $this;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $code ~ if not doing static file then set the Elements code here
     * @return $this
     */
    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $file - the file path
     *
     * @return $this
     */
    public function setAsStatic($file)
    {
        $this->media_source_id = 1;
        $this->static = true;
        $this->static_file = $file;
        return $this;
    }

    /**
     * @param string $category ~ nest like so: Category=>Child=>Child
     *
     * @return $this
     */
    public function setCategoryFromNames($category)
    {
        $this->category_names = explode('=>', $category);
        return $this;
    }

    /**
     * @return Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setProperties(array $data)
    {
        $this->properties->mergePropertiesFromArray($data);
        return $this;
    }

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
     * @param array $data
     *
     * @return $this
     */
    public function loadFromArray($data=[])
    {
        if ($data['static'] && !empty($data['static_file'])) {
            $this->setAsStatic($data['static_file']);
        }

        foreach ($data as $column => $value) {
            $method_name = 'set'.$this->makeStudyCase($column);
            if ($column == 'category') {
                $method_name = 'setCategoryFromNames';
            }

            if (method_exists($this, $method_name) && !is_null($value)) {
                if ($method_name == 'setProperties' && !is_array($value)) {
                    continue;
                }
                $this->$method_name($value);
            }
        }
        return $this;
    }

    public function saveFromRaw($data, $overwrite=false)
    {
        $saved = false;
        $this->element = $this->modx->getObject($this->element_class, ['name' => $this->name]);
        if (is_object($this->element) && !$overwrite) {
            $this->error = true;
            $this->error_messages['exits'] = 'Element: '.$this->name . ' of type '.$this->element_class.' already exists ';
            return $saved;
        } else {
            $this->element = $this->modx->newObject($this->element_class);
        }
        unset($data['id']);
        $this->element->fromArray($data);

        return $this->element->save();
    }

    /**
     * @param bool $overwrite
     *
     * @return bool
     */
    public function save($overwrite=false)
    {
        $saved = false;

        $this->element = $this->modx->getObject($this->element_class, [$this->name_column_name => $this->name]);
        if (is_object($this->element) && !$overwrite) {
            $this->error = true;
            $this->error_messages['exits'] = 'Element: '.$this->name . ' of type '.$this->element_class.' already exists ';
            return $saved;
        } else {
            $this->element = $this->modx->newObject($this->element_class);
        }

        $this->element->set($this->name_column_name, $this->name);

        if ( !empty($this->description)) {
            $this->element->set('description', $this->description);
        }

        if ($this->static) {
            $this->element->set('source', $this->media_source_id);
            $this->element->set('static', 1);
            $this->element->set('static_file', $this->static_file);
        } elseif ($this->static === false) {
            $this->element->set('source', 0);
            $this->element->set('static', 0);
            $this->element->set('static_file', '');
        }

        if ($this->code !== null) {
            // are all elements code column content?
            $this->element->set('content', $this->code);
        }
        // takes a string or array
        $this->element->set('properties', $this->properties->getData());

        $this->setAdditionalElementColumns();
        $this->relatedPieces();
        if ($this->element->save()) {
            //$this->climate->out($name.' plugin has been installed');
            $saved = true;
        } else {
            //$this->climate->error($name.' plugin did not install');

        }
        //sync?

        return $saved;
    }

    /**
     * @return int
     */
    protected function getCategoryIDFromNames()
    {
        $category_id = 0;
        $categories = $this->blender->getCategoryMap();
        $refresh = false;
        foreach ($this->category_names as $category) {
            if (isset($categories['names'][$category])) {
                $category_id = $categories['names'][$category];
            } else {
                $newCategory = $this->modx->newObject('modCategory');
                $newCategory->fromArray([
                    'parent' => $category_id,
                    'category' => $category,
                    'rank' => 0
                ]);
                $newCategory->save();
                $category_id = $newCategory->get('id');
                $refresh = true;
            }
        }
        $this->blender->getCategoryMap($refresh);
        return $category_id;
    }

    /**
     * @param int $category_id
     * @param string $string
     *
     * @return string
     */
    public function getCategoryAsString($category_id=0, $string='')
    {
        $categories = $this->blender->getCategoryMap();
        if (isset($categories['ids'][$category_id])) {
            $category = $categories['ids'][$category_id];
            if (empty($string)) {
                $string = $category['category'];
            } else {
                $string = $category['category'].'=>'.$string;
            }
            if ($category['parent'] > 0 ) {
                $string = $this->getCategoryAsString($category['parent'], $string);
            }
        }
        return $string;
    }
    /**
     * @param \modElement $element
     *
     * @return string
     */
    public function seedElement(\modElement $element)
    {
        // No IDs! must get the alias and get a seed key,
        $seed_key = $this->blender->getElementSeedKeyFromName($element->get($this->name_column_name), $this->element_class);
        $this->element_data = $element->toArray();

        $this->element_data['category'] = $this->getCategoryAsString($this->element_data['category']);

        $element = $this->seedRelated($element);

        // https://docs.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.invokeevent
        $this->modx->invokeEvent(
            'OnBlendSeedElement',
            [
                'blender' => $this->blender,
                'blendElement' => $this,
                'element_class' => $this->element_class,
                'element' => &$element,
                'data' => &$this->element_data
            ]
        );

        // now cache it:
        $this->modx->cacheManager->set(
            $seed_key,
            $this->element_data,
            $this->cache_life,
            $this->cacheOptions
        );

        return $seed_key;
    }
    /**
     * @param string $name
     *
     * @return bool|modElement
     */
    public function getElementFromName($name)
    {
        return $this->modx->getObject($this->element_class, [$this->name_column_name => $name]);
    }

    /**
     * @param string $seed_key
     * @param bool $overwrite
     *
     * @return bool
     */
    public function blend($seed_key, $overwrite=false)
    {
        $save = false;
        $this->loadElementDataFromSeed($seed_key);

        // does it exist
        $name = '';
        if (isset($this->element_data[$this->name_column_name])) {
            $this->setName($this->element_data[$this->name_column_name]);
            $name = $this->element_data[$this->name_column_name];
        }

        $down = false;
        $element = $this->getElementFromName($name);
        if ($element) {
            $this->exists = true;
            if (!$overwrite) {
                return $save;
            }
            $down = $element->toArray();
        } else {
            $this->exists = false;
        }

        unset($this->element_data['id']);

        // get template
        $this->modx->invokeEvent(
            'OnBlendElementBeforeSave',
            [
                'blender' => $this->blender,
                'blendResource' => $this,
                'element' => &$this->element,
                'data' => &$this->element_data
            ]
        );

        // load from array:
        $this->loadFromArray($this->element_data);
        $save = $this->save();

        if ($save) {
            // write old version to disk:
            $this->modx->cacheManager->set(
                'down-'.$seed_key,
                $down,
                $this->cache_life,
                $this->cacheOptions
            );
            $this->modx->invokeEvent(
                'OnBlendElementAfterSave',
                [
                    'blender' => $this->blender,
                    'blendResource' => $this,
                    'element' => &$this->element,
                    'data' => &$this->element_data
                ]
            );
        } else {
            $this->blender->out('Error did not save ', true);
        }
        return $save;
    }

    /**
     * @param string $seed_key
     *
     * @return bool
     */
    public function revertBlend($seed_key)
    {
        $this->loadElementDataFromSeed($seed_key);

        // does it exist
        $name = '';
        if (isset($this->element_data[$this->name_column_name])) {
            $this->setName($this->element_data[$this->name_column_name]);
            $name = $this->element_data[$this->name_column_name];
        }

        $element = $this->getElementFromName($name);
        if (!is_object($element)) {
            $element = $this->modx->getObject($this->element_class);
        }
        // 1. get previous data from cache:
        $data = $this->modx->cacheManager->get('down-'.$seed_key, $this->cacheOptions);

        if (!$data) {
            return $element->remove();

        } elseif (is_array($data)) {
            // load old data:
            $element->fromArray($data);
            return $element->save();
        }

        return false;
    }

    /**
     * @param string $seed_key
     *
     * @return $this
     */
    protected function loadElementDataFromSeed($seed_key)
    {
        $this->element_data = $this->modx->cacheManager->get($seed_key, $this->cacheOptions);
        $this->loadFromArray($this->element_data);
        return $this;
    }

    protected function setAdditionalElementColumns()
    {

    }

    protected function relatedPieces()
    {

    }

    /**
     * @param \modElement $element
     *
     * @return \modElement
     */
    protected function seedRelated($element)
    {
        return $element;
    }

}
