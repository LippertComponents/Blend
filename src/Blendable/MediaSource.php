<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 10/6/2017
 * Time: 9:01 AM
 */

namespace LCI\Blend\Blendable;


class MediaSource extends Blendable
{
    use BlendableProperties;
    use DescriptionGetterAndSetter;
    // return $this->blender->getElementSeedKeyFromName($this->getFieldName(), $this->xpdo_simple_object_class)

    /** @var string  */
    protected $opt_cache_key = 'media-sources';

    /** @var string ex: modResource */
    protected $xpdo_simple_object_class = 'modMediaSource';

    /** @var string  */
    protected $unique_key_column = 'name';

    /** @var array ~ this should match data to be inserted via xPDO, ex [column_name => value, ...] */
    protected $blendable_xpdo_simple_object_data = [
        'name' => '',
        'description' => '',
        'class_key' => 'sources.modFileMediaSource',
        //`properties` mediumtext,
        'is_stream' => 1
    ];

    /** @var array ~ ['setMethodName' => 'setMethodActualName', 'setDoNotUseMethod' => false] overwrite in child classes */
    protected $load_from_array_aliases = [
        'setProperties' => 'mergePropertiesFromArray'
    ];

    protected $mediaSourceProcessor = null;

    // create methods to match the column data:

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->blendable_xpdo_simple_object_data['name'];
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
     * @return bool
     */
    public function getFieldClassKey()
    {
        return $this->blendable_xpdo_simple_object_data['class_key'];
    }

    /**
     * @param string $class_key
     * @return $this
     */
    public function setFieldClassKey($class_key)
    {
        $this->blendable_xpdo_simple_object_data['class_key'] = $class_key;
        return $this;
    }

    /**
     * @return bool
     */
    public function getFieldIsStream()
    {
        return $this->blendable_xpdo_simple_object_data['is_stream'];
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function setFieldIsStream($bool)
    {
        $this->blendable_xpdo_simple_object_data['is_stream'] = (int)$bool;
        return $this;
    }

    /**
     * property helpers:
     * @SEE: core/model/modx/sources/modfilemediasource.class.php -> getDefaultProperties
     * @SEE: core/model/modx/sources/mods3mediasource.class.php -> getDefaultProperties
     */


    /**
     * @param string $value
     * @return $this
     */
    public function setPropertyBasePath($value)
    {
        $this->properties->setProperty('basePath', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPropertyBasePathRelative($value)
    {
        $this->properties->setProperty('basePathRelative', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPropertyBaseUrl($value)
    {
        $this->properties->setProperty('baseUrl', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPropertyBaseUrlRelative($value)
    {
        $this->properties->setProperty('baseUrlRelative', $value);
        return $this;
    }

    /**
     * @param string $value ~ comma separated list, ex: jpg,jpeg,png,gif,svg
     * @return $this
     */
    public function setPropertyAllowedFileTypes($value)
    {
        $this->properties->setProperty('allowedFileTypes', $value);
        return $this;
    }

    /**
     * @param string $value ~ comma separated list, ex: jpg,jpeg,png,gif,svg
     * @return $this
     */
    public function setPropertyImageExtensions($value)
    {
        $this->properties->setProperty('imageExtensions', $value);
        return $this;
    }

    /**
     * @param string $value ~ one of: jpg,png,gif
     * @return $this
     */
    public function setPropertyThumbnailType($value)
    {
        $this->properties->setProperty('thumbnailType', $value);
        return $this;
    }

    /**
     * @param string $value ~ 1 to 100, default=90
     * @return $this
     */
    public function setPropertyThumbnailQuality($value)
    {
        $this->properties->setProperty('thumbnailQuality', $value);
        return $this;
    }

    /**
     * @param string $value ~ public or private
     * @return $this
     */
    public function setPropertyVisibility($value)
    {
        $this->properties->setProperty('visibility', $value);
        return $this;
    }
    /**
     * @param string $value ~ comma separated list: .svn,.git,_notes,nbproject,.idea,.DS_Store
     * @return $this
     */
    public function setPropertySkipFiles($value)
    {
        $this->properties->setProperty('skipFiles', $value);
        return $this;
    }

    /**
     * @param string $value ~ AWS S3: The URL of the Amazon S3 instance.
     * @return $this
     */
    public function setPropertyUrl($value)
    {
        $this->properties->setProperty('url', $value);
        return $this;
    }
    /**
     * @param string $value ~ AWS S3: Region of the bucket. Example: us-west-1
     * @return $this
     */
    public function setPropertyRegion($value)
    {
        $this->properties->setProperty('region', $value);
        return $this;
    }
    /**
     * @param string $value ~ AWS S3: The S3 Bucket to load your data from.
     * @return $this
     */
    public function setPropertyBucket($value)
    {
        $this->properties->setProperty('bucket', $value);
        return $this;
    }
    /**
     * @param string $value ~ AWS S3: Optional path/folder prefix
     * @return $this
     */
    public function setPropertyPrefix($value)
    {
        $this->properties->setProperty('prefix', $value);
        return $this;
    }
    /**
     * @param string $value ~ AWS S3: The Amazon key for authentication to the bucket.
     * @return $this
     */
    public function setPropertyKey($value)
    {
        $this->properties->setProperty('Key', $value);
        return $this;
    }
    /**
     * @param string $value ~ AWS S3: The Amazon secret key for authentication to the bucket.
     * @return $this
     */
    public function setPropertySecretKey($value)
    {
        $this->properties->setProperty('secret_key', $value);
        return $this;
    }

    /**
     * Properly format the data here:
     * @return array
     */
    public function getPropertiesData()
    {
        $data = $this->properties->getData();

        $class = $this->modx->loadClass($this->blendable_xpdo_simple_object_data['class_key']);
        $mediaObject = new $class($this->modx);

        if (is_object($mediaObject) && method_exists($mediaObject, 'getDefaultProperties')) {
            $default_props = $mediaObject->getDefaultProperties();

            foreach ($default_props as $key => $value) {
                $set_value = $value;
                if (isset($data[$key])) {
                    $set_value = $data[$key];
                }
                $data[$key] = $set_value;

                if (isset($data[$key]) && is_array($value) && is_string($set_value)) {
                    $data[$key] = $value;
                    $data[$key]['value'] = $set_value;
                }
            }
        }

        return $data;
    }

    /**
     * @return Blendable
     */
    public function getCurrentVersion()
    {
        /** @var MediaSource $mediaSource */
        $mediaSource = new self($this->modx, $this->blender, $this->getFieldName());
        return $mediaSource->setSeedsDir($this->getSeedsDir());
    }
}
