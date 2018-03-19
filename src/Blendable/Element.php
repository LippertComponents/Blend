<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 11:47 AM
 */

namespace LCI\Blend\Blendable;


abstract class Element extends Blendable
{
    use BlendableProperties;
    use DescriptionGetterAndSetter;

    /** @var string  */
    protected $opt_cache_key = 'elements';

    /** @var string ex: modResource */
    protected $xpdo_simple_object_class = 'modElement';

    /** @var string  */
    protected $unique_key_column = 'name';

    /** @var array ~ this should match data to be inserted via xPDO, ex [column_name => value, ...] */
    protected $blendable_xpdo_simple_object_data = [
        'category' => '',
        //'cache_type' => 0,//bool
        //content: 'snippet', plugincode
        'description' => '',// 191
        'editor_type' => 0, // int?
        'locked' => 0, // bool
        'name' => '',
        'property_preprocess' => 0,// bool
        'properties' => '',//??
        'source' => 1,
        'static' =>  0,
        'static_file' => ''
    ];

    /** @var array ~ ['setMethodName' => 'setMethodActualName', 'setDoNotUseMethod' => false] overwrite in child classes */
    protected $load_from_array_aliases = [
        'setFieldProperties' => 'mergePropertiesFromArray'
    ];

    /** @var bool  */
    protected $overwrite_static = false;

    /**
     * @return string
     */
    public function getFieldCode()
    {
        return $this->getFieldContent();
    }

    /**
     * @return string
     */
    public function getFieldContent()
    {
        return $this->blendable_xpdo_simple_object_data['content'];
    }

    /**
     * @return int
     */
    public function getFieldEditorType()
    {
        return $this->blendable_xpdo_simple_object_data['editor_type'];
    }

    /**
     * @return bool
     */
    public function getFieldLocked()
    {
        return $this->blendable_xpdo_simple_object_data['locked'];
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->blendable_xpdo_simple_object_data['name'];
    }

    /**
     * @return bool
     */
    public function getFieldPropertyPreprocess()
    {
        return $this->blendable_xpdo_simple_object_data['property_preprocess'];
    }

    // Setters:

    /**
     * @param string $category ~ nest like so: Category=>Child=>Child
     *
     * @return $this
     */
    public function setFieldCategory($category)
    {
        $this->blendable_xpdo_simple_object_data['category'] = $category;
        return $this;
    }

    /**
     * @param string $code ~ if not doing static file then set the Elements code here
     * @param bool $overwrite_static ~ if the setAsStatic is ran, false will keep the static content code, true will overwrite the static file
     * @return $this
     */
    public function setFieldCode($code, $overwrite_static=false)
    {
        $this->blendable_xpdo_simple_object_data['content'] = $code;
        $this->overwrite_static = $overwrite_static;
        return $this;
    }

    /**
     * duplicate method for setCode, matches MODX naming
     * @param string $code ~ if not doing static file then set the Elements code here
     * @param bool $overwrite_static ~ if the setAsStatic is ran, false will keep the static content code, true will overwrite the static file
     * @return $this
     */
    public function setFieldContent($code, $overwrite_static=false)
    {
        return $this->setFieldCode($code, $overwrite_static);
    }
    /**
     * @param int $value
     * @return $this
     */
    public function setFieldEditorType($value)
    {
        $this->blendable_xpdo_simple_object_data['editor_type'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldLocked($value)
    {
        $this->blendable_xpdo_simple_object_data['locked'] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setFieldName($name)
    {
        $this->blendable_xpdo_simple_object_data['name'] = $name;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldPropertyPreprocess($value)
    {
        $this->blendable_xpdo_simple_object_data['property_preprocess'] = $value;
        return $this;
    }

    /**
     * @param string $file - the file path
     * @param string $media_source ~ name of the media source
     *
     * @return $this
     */
    public function setAsStatic($file, $media_source='Filesystem')
    {
        $this->blendable_xpdo_simple_object_data['source'] = $media_source;
        $this->blendable_xpdo_simple_object_data['static'] = true;
        $this->blendable_xpdo_simple_object_data['static_file'] = $file;
        return $this;
    }

    /**
     * @param string $category
     * @return int
     */
    protected function convertCategory($category)
    {
        $categories = explode('=>', $category);

        $category_names = [];
        $lineage = '';

        $count = 0;
        foreach ($categories as $category) {
            if (!empty($lineage)) {
                $lineage .= '=>';
            }
            $lineage .= trim($category);

            $category_names[$count++] = ['name' => $category, 'lineage' => $lineage];
        }

        $category_id = 0;
        $category_map = $this->blender->getCategoryMap();
        $refresh = false;
        foreach ($category_names as $count => $name_data) {
            $category = $name_data['name'];
            $lineage = $name_data['lineage'];

            if (isset($category_map['lineage'][$lineage]) && isset($category_map['lineage'][$lineage]['id'])) {
                $category_id = $category_map['lineage'][$lineage]['id'];

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
     * @param array $media_source
     * @return int
     */
    protected function convertSource($media_source)
    {
        if (empty($media_source)) {
            return 1;
        }
        $id = 1;

        $mediaSource = $this->modx->getObject('modMediaSource', ['name' => $media_source]);
        if (is_object($mediaSource)) {
            $id = $mediaSource->get('id');
        }
        return $id;
    }

    /**
     * @param int $category_id
     *
     * @return string
     */
    public function seedCategory($category_id=0)
    {
        $categories = $this->blender->getCategoryMap();
        if (isset($categories['ids'][$category_id]) && isset($categories['ids'][$category_id]['lineage'])) {
            return $categories['ids'][$category_id]['lineage'];
        }

        return '';
    }
    /**
     * @param int $media_source_id
     * @return string
     */
    protected function seedSource($media_source_id)
    {
        $name = 'Filesystem';

        $mediaSource = $this->modx->getObject('modMediaSource', $media_source_id);
        if (is_object($mediaSource)) {
            $name = $mediaSource->get('name');
        }
        return $name;
    }
}
