<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 10/6/2017
 * Time: 9:01 AM
 */

namespace LCI\Blend\Blendable;
use LCI\Blend\Blender;

/**
 * Class Resource
 * @package LCI\Blend\Blendable
 */
class Resource extends Blendable
{
    use BlendableProperties;

    /** @var string  */
    protected $opt_cache_key = 'resources/web';

    /** @var string ex: modResource */
    protected $xpdo_simple_object_class = 'modResource';

    /** @var string  */
    protected $unique_key_column = 'alias';

    /** @var array ~ this should match data to be inserted via xPDO, ex [column_name => value, ...] */
    protected $blendable_xpdo_simple_object_data = [
        'alias' => '',
        'alias_visible' => true,
        'cacheable' => true,
        'class_key' => 'modDocument',
        'content' => '',
        'contentType' => 'text/html',
        'content_dispo' => false,
        'context_key' => 'web',
        'createdby' => 0,
        'deleted' => false,
        'description' => '',
        'donthit' => false,
        'hidemenu' => false,
        'hide_children_in_tree' => false,
        'introtext' => '',
        'isfolder' => false,
        'link_attributes' => '',
        'longtitle' => '',
        'menuindex' => 0,
        'menutitle' => '',
        'pagetitle' => '',
        'parent' => 0,
        'privatemgr' => false,
        'privateweb' => false,
        'properties' => '',
        'published' => false,
        'publishedby' => 0,
        'publishedon' => 0,
        'pub_date' => 0,
        'richtext' => true,
        'searchable' => true,
        'show_in_tree' => true,
        'template' => 0,
        'type' => 'document',
        'unpub_date' => 0,
        'uri' => '',
        'uri_override' => false,
    ];

    /** @var array ~ ['setMethodName' => 'setMethodActualName', 'setDoNotUseMethod' => false] overwrite in child classes */
    protected $load_from_array_aliases = [
        'setProperties' => 'mergePropertiesFromArray'
    ];

    /**
     * Resource constructor.
     *
     * @param \modx $modx
     * @param Blender $blender
     * @param string $alias ~ alias as string
     * @param string $context ~ will default to web context
     */
    public function __construct(\modx $modx, Blender $blender, $alias, $context = 'web')
    {
        $this->setFieldAlias($alias);
        $this->setFieldContextKey($context);
        parent::__construct($modx, $blender, ['alias' => $alias, 'context_key' => $context]);
    }


    // Column Getters:
    /**
     * @return string
     */
    public function getFieldAlias()
    {
        return $this->blendable_xpdo_simple_object_data['alias'];
    }

    /**
     * @return bool
     */
    public function getFieldAliasVisible()
    {
        return $this->blendable_xpdo_simple_object_data['alias_visible'];
    }

    /**
     * @return bool
     */
    public function getFieldCacheable()
    {
        return $this->blendable_xpdo_simple_object_data['cacheable'];
    }

    /**
     * @return string
     */
    public function getFieldClassKey()
    {
        return $this->blendable_xpdo_simple_object_data['class_key'];
    }

    /**
     * @return string
     */
    public function getFieldContent()
    {
        return $this->blendable_xpdo_simple_object_data['content'];
    }

    /**
     * @return string
     */
    public function getFieldContentType()
    {
        return $this->blendable_xpdo_simple_object_data['contentType'];
    }

    /**
     * @return bool
     */
    public function getFieldContentDispo()
    {
        return $this->blendable_xpdo_simple_object_data['content_dispo'];
    }

    /**
     * @return string
     */
    public function getFieldContextKey()
    {
        return $this->blendable_xpdo_simple_object_data['context_key'];
    }

    /**
     * @return int
     */
    public function getFieldCreatedby()
    {
        return $this->blendable_xpdo_simple_object_data['createdby'];
    }

    /**
     * @return bool
     */
    public function getFieldDeleted()
    {
        return $this->blendable_xpdo_simple_object_data['deleted'];
    }

    /**
     * @return string
     */
    public function getFieldDescription()
    {
        return $this->blendable_xpdo_simple_object_data['description'];
    }

    /**
     * @return bool
     */
    public function getFieldDonthit()
    {
        return $this->blendable_xpdo_simple_object_data['donthit'];
    }

    /**
     * @return bool
     */
    public function getFieldHidemenu()
    {
        return $this->blendable_xpdo_simple_object_data['hidemenu'];
    }

    /**
     * @return bool
     */
    public function getFieldHideChildrenInTree()
    {
        return $this->blendable_xpdo_simple_object_data['hide_children_in_tree'];
    }

    /**
     * @return string
     */
    public function getFieldIntrotext()
    {
        return $this->blendable_xpdo_simple_object_data['introtext'];
    }

    /**
     * @return bool
     */
    public function getFieldIsfolder()
    {
        return $this->blendable_xpdo_simple_object_data['isfolder'];
    }

    /**
     * @return string
     */
    public function getFieldLinkAttributes()
    {
        return $this->blendable_xpdo_simple_object_data['link_attributes'];
    }

    /**
     * @return string
     */
    public function getFieldLongtitle()
    {
        return $this->blendable_xpdo_simple_object_data['longtitle'];
    }

    /**
     * @return int
     */
    public function getFieldMenuindex()
    {
        return $this->blendable_xpdo_simple_object_data['menuindex'];
    }

    /**
     * @return string
     */
    public function getFieldMenutitle()
    {
        return $this->blendable_xpdo_simple_object_data['menutitle'];
    }

    /**
     * @return string
     */
    public function getFieldPagetitle()
    {
        return $this->blendable_xpdo_simple_object_data['pagetitle'];
    }

    /**
     * @return int
     */
    public function getFieldParent()
    {
        return $this->blendable_xpdo_simple_object_data['parent'];
    }

    /**
     * @return bool
     */
    public function getFieldPrivatemgr()
    {
        return $this->blendable_xpdo_simple_object_data['privatemgr'];
    }

    /**
     * @return bool
     */
    public function getFieldPrivateweb()
    {
        return $this->blendable_xpdo_simple_object_data['privateweb'];
    }

    /**
     * @return string
     */
    public function getFieldProperties()
    {
        return $this->blendable_xpdo_simple_object_data['properties'];
    }

    /**
     * @return bool
     */
    public function getFieldPublished()
    {
        return $this->blendable_xpdo_simple_object_data['published'];
    }

    /**
     * @return int
     */
    public function getFieldPublishedby()
    {
        return $this->blendable_xpdo_simple_object_data['publishedby'];
    }

    /**
     * @return int
     */
    public function getFieldPublishedon()
    {
        return $this->blendable_xpdo_simple_object_data['publishedon'];
    }

    /**
     * @return int
     */
    public function getFieldPubDate()
    {
        return $this->blendable_xpdo_simple_object_data['pub_date'];
    }

    /**
     * @return bool
     */
    public function getFieldRichtext()
    {
        return $this->blendable_xpdo_simple_object_data['richtext'];
    }

    /**
     * @return bool
     */
    public function getFieldSearchable()
    {
        return $this->blendable_xpdo_simple_object_data['searchable'];
    }

    /**
     * @return bool
     */
    public function getFieldShowInTree()
    {
        return $this->blendable_xpdo_simple_object_data['show_in_tree'];
    }

    /**
     * @return int
     */
    public function getFieldTemplate()
    {
        return $this->blendable_xpdo_simple_object_data['template'];
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        return $this->blendable_xpdo_simple_object_data['type'];
    }

    /**
     * @return int
     */
    public function getFieldUnpubDate()
    {
        return $this->blendable_xpdo_simple_object_data['unpub_date'];
    }

    /**
     * @return string
     */
    public function getFieldUri()
    {
        return $this->blendable_xpdo_simple_object_data['uri'];
    }

    /**
     * @return bool
     */
    public function getFieldUriOverride()
    {
        return $this->blendable_xpdo_simple_object_data['uri_override'];
    }

    // Column Setters:
    /**
     * @param string $value  max characters: 191
     * @return $this
     */
    public function setFieldAlias($value)
    {
        $this->blendable_xpdo_simple_object_data['alias'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldAliasVisible($value)
    {
        $this->blendable_xpdo_simple_object_data['alias_visible'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldCacheable($value)
    {
        $this->blendable_xpdo_simple_object_data['cacheable'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 100
     * @return $this
     */
    public function setFieldClassKey($value)
    {
        $this->blendable_xpdo_simple_object_data['class_key'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldContent($value)
    {
        $this->blendable_xpdo_simple_object_data['content'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 50
     * @return $this
     */
    public function setFieldContentType($value)
    {
        $this->blendable_xpdo_simple_object_data['contentType'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldContentDispo($value)
    {
        $this->blendable_xpdo_simple_object_data['content_dispo'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 100
     * @return $this
     */
    public function setFieldContextKey($value)
    {
        $this->blendable_xpdo_simple_object_data['context_key'] = $value;
        $this->opt_cache_key = 'resources/'.$value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldCreatedby($value)
    {
        $this->blendable_xpdo_simple_object_data['createdby'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldDeleted($value)
    {
        $this->blendable_xpdo_simple_object_data['deleted'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 191
     * @return $this
     */
    public function setFieldDescription($value)
    {
        $this->blendable_xpdo_simple_object_data['description'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldDonthit($value)
    {
        $this->blendable_xpdo_simple_object_data['donthit'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldHidemenu($value)
    {
        $this->blendable_xpdo_simple_object_data['hidemenu'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldHideChildrenInTree($value)
    {
        $this->blendable_xpdo_simple_object_data['hide_children_in_tree'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldIntrotext($value)
    {
        $this->blendable_xpdo_simple_object_data['introtext'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldIsfolder($value)
    {
        $this->blendable_xpdo_simple_object_data['isfolder'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 191
     * @return $this
     */
    public function setFieldLinkAttributes($value)
    {
        $this->blendable_xpdo_simple_object_data['link_attributes'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 191
     * @return $this
     */
    public function setFieldLongtitle($value)
    {
        $this->blendable_xpdo_simple_object_data['longtitle'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldMenuindex($value)
    {
        $this->blendable_xpdo_simple_object_data['menuindex'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 191
     * @return $this
     */
    public function setFieldMenutitle($value)
    {
        $this->blendable_xpdo_simple_object_data['menutitle'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 191
     * @return $this
     */
    public function setFieldPagetitle($value)
    {
        $this->blendable_xpdo_simple_object_data['pagetitle'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldParent($value)
    {
        $this->blendable_xpdo_simple_object_data['parent'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldPrivatemgr($value)
    {
        $this->blendable_xpdo_simple_object_data['privatemgr'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldPrivateweb($value)
    {
        $this->blendable_xpdo_simple_object_data['privateweb'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldProperties($value)
    {
        $this->blendable_xpdo_simple_object_data['properties'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldPublished($value)
    {
        $this->blendable_xpdo_simple_object_data['published'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldPublishedby($value)
    {
        $this->blendable_xpdo_simple_object_data['publishedby'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldPublishedon($value)
    {
        $this->blendable_xpdo_simple_object_data['publishedon'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldPubDate($value)
    {
        $this->blendable_xpdo_simple_object_data['pub_date'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldRichtext($value)
    {
        $this->blendable_xpdo_simple_object_data['richtext'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldSearchable($value)
    {
        $this->blendable_xpdo_simple_object_data['searchable'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldShowInTree($value)
    {
        $this->blendable_xpdo_simple_object_data['show_in_tree'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldTemplate($value)
    {
        $this->blendable_xpdo_simple_object_data['template'] = $value;
        return $this;
    }

    /**
     * @param string $value  max characters: 20
     * @return $this
     */
    public function setFieldType($value)
    {
        $this->blendable_xpdo_simple_object_data['type'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldUnpubDate($value)
    {
        $this->blendable_xpdo_simple_object_data['unpub_date'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldUri($value)
    {
        $this->blendable_xpdo_simple_object_data['uri'] = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFieldUriOverride($value)
    {
        $this->blendable_xpdo_simple_object_data['uri_override'] = $value;
        return $this;
    }


    /**
     * @return array
     */
    protected function getUniqueCriteria()
    {
        return [
            $this->unique_key_column => $this->blendable_xpdo_simple_object_data[$this->unique_key_column],
            'context_key' => $this->getFieldContextKey()
        ];
    }

    /**
     * @param string|array $criteria ~ alias as string will default to web context or ['alias' => 'my-page.html', 'context_key' => 'web']
     */
    protected function setUniqueCriteria($criteria)
    {
        if (is_string($criteria)) {
            $this->setFieldAlias($criteria);
        } else {
            $this->setFieldAlias($criteria['alias']);
            $this->setFieldContextKey($criteria['context_key']);
        }
    }

    /**
     *
     */
    protected function assignResourceGroups()
    {
        $new_groups = (isset($this->related_data['resource_groups']) ? $this->related_data['resource_groups'] : [] );

        // resource groups
        $current_groups = $this->xPDOSimpleObject->getResourceGroupNames();
        foreach ($current_groups as $group) {
            if (!in_array($group, $new_groups)) {
                $this->xPDOSimpleObject->leaveGroup($group);
            }
        }

        foreach ($new_groups as $group) {
            /** @var \modResourceGroup $resourceGroup */
            $resourceGroup = $this->modx->getObject('modResourceGroup', ['name' => $group]);
            if (!$resourceGroup || !$resourceGroup instanceof \modResourceGroup) {
                // create the resource group if it does not exist
                $this->blender->out('Attempting to create a new Resource Group: '.$group);
                $resourceGroup = $this->modx->newObject('modResourceGroup');
                $resourceGroup->set('name', $group);
                $resourceGroup->save();
            }

            if (!in_array($group, $current_groups)) {
                $this->xPDOSimpleObject->joinGroup($group);
            }
        }
    }

    /**
     * @param string $seed_key
     * @param string $context
     *
     * @return bool|\modResource
     */
    public function getResourceFromSeedKey($seed_key, $context='web')
    {
        // get the alias:
        $alias = $this->blender->getAliasFromSeedKey($seed_key);

        return $this->modx->getObject('modResource', ['alias' => $alias, 'context_key' => $context]);
    }

    /**
     * @param string $type ~ seed or revert
     * @return string
     */
    public function getSeedKey($type='seed')
    {
        $key = $this->blender->getSeedKeyFromAlias($this->blendable_xpdo_simple_object_data['alias']);

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
     * @param array $resource_data
     *
     * @return $this
     */
    public function setResourceData(array $resource_data)
    {
        $this->blendable_xpdo_simple_object_data = $resource_data;
        return $this;
    }


    /**
     * @return Blendable
     */
    public function getCurrentVersion()
    {
        /** @var \LCI\Blend\Blendable\Resource $resource */
        $resource = new self($this->modx, $this->blender, $this->getFieldAlias(), $this->getFieldContextKey());
        return $resource
            ->setSeedsDir($this->getSeedsDir());
    }

    /**
     * Override in child classes
     */
    protected function loadRelatedData()
    {
        /** @var \modResource $this->xPDOSimpleObject */
        $this->xPDOSimpleObject;
        // no IDs only TV name
        $tvs = [];// TemplateVarResources modTemplateVarResource

        $template = false;
        $resource_groups = [];
        if (is_object($this->xPDOSimpleObject)) {
            /** @var \modTemplate $template */
            $template = $this->xPDOSimpleObject->getOne('Template');
            $resource_groups = $this->xPDOSimpleObject->getResourceGroupNames();
        }
        if (is_object($template)) {

            // get all TemplateValues
            $tvTemplates = $template->getMany('TemplateVarTemplates');
            /** @var \modTemplateVarTemplates $tvTemplate */
            foreach ($tvTemplates as $tvTemplate) {
                /** @var \modTemplateVar $tv */
                $tv = $tvTemplate->getOne('TemplateVar');
                $tv_name = $tv->get('name');

                $tvs[$tv_name] = [
                    'type' => $tv->get('type'),
                    'value' => $this->xPDOSimpleObject->getTVValue($tv_name)
                ];

                switch ($tv->get('type')) {
                    case 'resourcelist':
                        if ($tvs[$tv_name]['value'] > 0) {
                            $tvs[$tv_name]['portable_value'] = $this->blender->getResourceSeedKeyFromID($tvs[$tv_name]);
                        }
                        break;
                }
            }
        }

        $extras = [];
        // tagger:
        $tagger = $this->blender->getTagger();
        if ($tagger instanceof \Tagger) {
            $extras['tagger'] = $this->getResourceTags();
        }

        $this->related_data = [
            'extras' => $extras,
            'tvs' => $tvs,
            'resource_groups' => $resource_groups
        ];

        // @TODO need event to allow others to add in extras here:

    }

    protected function uniqueCriteria()
    {
        return [
            $this->unique_key_column => $this->blendable_xpdo_simple_object_data[$this->unique_key_column],
            'context' => $this->getFieldContextKey()
        ];
    }

    /**
     * Create convert methods for any portable data column that needs to be converted to an int for a related primary key
     */
    /**
     * @param array $parent
     * @return int
     */
    protected function convertParent($parent)
    {
        if (!is_array($parent)) {
            // @TODO throw exception
            return 0;
        }
        return (int) $this->blender->getResourceIDFromSeedKey(
            $parent['seed_key'],
            $parent['context']
        );
    }
    /**
     * @param string $name
     * @return int
     */
    protected function convertTemplate($name)
    {
        $id = 0;

        // Should there be an event fired here? Allowing to alter the name or returned ID?
        $template = $this->modx->getObject('modTemplate', ['templatename' => $name]);
        if ($template) {
            $id = $template->get('id');
            $this->blender->out(' Template ID set: '.$id);
        } else {
            $this->blender->out(' Template not found: '.$name, true);
        }
        return $id;
    }

    /**
     * Create seed methods for any column that needs be portable, from an int to string|array
     */
    /**
     * @param int $parent_id
     * @return array
     */
    protected function seedParent($parent_id)
    {
        return $this->blender->getResourceSeedKeyFromID($parent_id);
    }

    /**
     * @param int $template_id
     * @return string
     */
    protected function seedTemplate($template_id)
    {
        $template = $this->xPDOSimpleObject->getOne('Template');

        if (is_object($template)) {
            return $template->get('templatename');
        }
        return '';
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
        if (isset($this->related_data['tvs'])) {
            $tvs = $this->related_data['tvs'];
            if (is_array($tvs) && count($tvs) > 0) {
                foreach ($tvs as $tv_name => $tv_data) {
                    //$this->blender->out('  set TV: '.$tv_name.' '.$value);
                    $value = $tv_data['value'];

                    switch ($tv_data['type']) {
                        case 'resourcelist':
                            if (isset($tv_data['portable_value']) && isset($tv_data['portable_value']['context']) && isset($tv_data['portable_value']['seed_key'])) {
                                $value = $this->blender->getResourceIDFromSeedKey($tv_data['portable_value']['seed_key'], $tv_data['portable_value']['context']);
                            }
                            break;
                    }

                    // Event here?

                    $this->xPDOSimpleObject->setTVValue($tv_name, $value);
                }
            }
        }

        $this->assignResourceGroups();

        // extras
        $tagger = $this->blender->getTagger();
        if ($tagger instanceof \Tagger) {
            $this->setResourceTags($this->xPDOSimpleObject, (isset($this->related_data['extras']['tagger']) ? $this->related_data['extras']['tagger'] : []));
        }
    }

    // current_xpdo_simple_object_data for seeds: and seedRelated


    // Extras, to be removed
    /**
     * @return array
     */
    protected function getResourceTags()
    {
        $tags = [];
        [
            'group-alias' => [
                'tags' => [],
                'columns' => []
            ]
        ];
        // get resource Group
        // Get all tags for resource:

        $query = $this->modx->newQuery('TaggerTag');

        $query->leftJoin('TaggerTagResource', 'Resources');
        $query->leftJoin('TaggerGroup', 'Group');
        $query->leftJoin('modResource', 'Resource', ['Resources.resource = Resource.id']);

        $query->select($this->modx->getSelectColumns('TaggerTag', 'TaggerTag'));
        $query->select($this->modx->getSelectColumns('TaggerGroup', 'Group', 'group_'));

        $query->where(['Resources.resource' => $this->xPDOSimpleObject->get('id')]);

        $query->prepare();
        $sql = $query->toSql();

        $results = $this->modx->query($sql);

        while ($tag = $results->fetch(\PDO::FETCH_ASSOC)) {

            $tag_columns = $group_columns = [];
            foreach ($tag as $name => $value) {
                if (strpos(' '.$name, 'group_') === 1) {
                    $group_columns[substr($name, strlen('group_'))] = $value;
                } else {
                    $tag_columns[$name] = $value;
                }
            }
            if ( !isset($tags[$tag['group_name']])) {
                $tags[$tag['group_alias']] = [
                    'columns' => $group_columns,
                    'tags' => []
                ];
            }

            $tags[$tag['group_alias']]['tags'][$tag['alias']] = $tag_columns;
        }

        return $tags;
    }

    /**
     * @param \modResource $resource
     * @param array $tags
     */
    protected function setResourceTags(\modResource $resource, $tags=[])
    {
        $tagger_groups = [];

        $existing_tags = $this->getResourceTags($resource);
        foreach ($existing_tags as $tag_group_alias => $data) {

            $tagger_groups[$tag_group_alias] = $data['columns']['id'];

            $remove = false;
            if (!isset($tags[$tag_group_alias])) {
                // remove all related group tags as
                $remove = true;
            }
            if (isset($data['tags'])) {
                foreach ($data['tags'] as $tag_alias => $tag) {
                    $not_current = false;
                    if (!isset($tags[$tag_group_alias]) || !isset($tags[$tag_group_alias]['tags']) || !isset($tags[$tag_group_alias]['tags'][$tag_alias])) {
                        $not_current = true;
                    } else {
                        // it already exists so remove from the save list
                        unset($tags[$tag_group_alias]['tags'][$tag_alias]);
                    }
                    if ($remove || $not_current) {
                        $resourceTag = $this->modx->getObject('TaggerTagResource', ['tag' => $tag['id'], 'resource' => $resource->get('id')]);
                        if ($resourceTag) {
                            $resourceTag->remove();
                        }
                    }
                }
            }
        }

        // now save any remaining tags/groups
        foreach ($tags as $tag_group_alias => $data) {
            // does the tagger group exist?
            if (!$tagger_groups[$tag_group_alias]) {
                $taggerGroup = $this->modx->getObject('TaggerGroup', ['alias' => $tag_group_alias]);
                if (!$taggerGroup) {
                    $taggerGroup = $this->modx->newObject('TaggerGroup');
                    unset($data['columns']['id']);
                    $taggerGroup->fromArray($data['columns']);
                    // @TODO show for templates column
                    $taggerGroup->save();
                }
                $tagger_groups[$tag_group_alias] = $taggerGroup->get('id');
            }


            if (isset($data['tags'])) {
                foreach ($data['tags'] as $tag_alias => $tag) {
                    // does the tag exist?
                    $taggerTag = $this->modx->getObject('TaggerTag', ['alias' => $tag_alias]);
                    if (!$taggerTag) {
                        $taggerTag = $this->modx->newObject('TaggerTag');
                        unset($tag['id']);
                        $taggerTag->fromArray($tag);
                        $taggerTag->save();
                    }

                    $resourceTag = $this->modx->newObject('TaggerTagResource');
                    $resourceTag->set('tag', $taggerTag->get('id'));
                    $resourceTag->set('resource', $resource->get('id'));
                    $resourceTag->save();
                }
            }
        }

    }
}
