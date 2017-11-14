<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 10/6/2017
 * Time: 9:01 AM
 */

namespace LCI\Blend;


class Resource
{

    /** @var  \modx */
    protected $modx;

    /** @var Blender  */
    protected $blender;

    protected $exists = false;

    /** @var array  */
    protected $cacheOptions = [];

    /** @var string */
    protected $timestamp = '';

    /** @var int $cache_life in seconds, 0 is forever */
    protected $cache_life = 0;

    /** @var array  */
    protected $resource_data = [];

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

        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => 'resources',
            \xPDO::OPT_CACHE_PATH => $this->blender->getSeedsDirectory()
        ];
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
     * @param string $seed_key
     *
     * @return bool|array
     */
    protected function loadResourceDataFromSeed($seed_key)
    {
        $this->resource_data = $this->modx->cacheManager->get($seed_key, $this->cacheOptions);
        return $this->resource_data;
    }

    public function blendResource($seed_key, $overwrite=false)
    {
        $save = false;
        $this->loadResourceDataFromSeed($seed_key);
        // does it exist
        $resource = $this->getResourceFromSeedKey($seed_key);
        if ($resource) {
            $this->exists = true;
            if (!$overwrite) {
                $this->blender->out('   Current ID: '. $resource->get('id').' -- '. $resource->get('pagetitle'));
                $this->blender->out('   Seed ID: '. $this->resource_data['id'].' -- '. $this->resource_data['pagetitle']);
                return $save;
            }
        } else {
            $resource = $this->modx->newObject('modResource');
        }
        $org_id = $this->resource_data['id'];
        unset($this->resource_data['id']);

        $tvs = $this->resource_data['tv'];
        unset($this->resource_data['tv']);

        $extras = $this->resource_data['extras'];
        unset($this->resource_data['extras']);

        // get parent ID
        $this->resource_data['parent'] = $this->blender->getResourceIDFromSeedKey($this->resource_data['parent']);

        // get template
        $this->resource_data['template'] = $this->getTemplateIDFromName($this->resource_data['template'], $resource);

        $this->modx->invokeEvent(
            'OnBlendResourceBeforeSave',
            [
                'blender' => $this->blender,
                'blendResource' => $this,
                'resource' => &$resource,
                'tvs' => $tvs,
                'extras' => $extras,
                'data' => &$this->resource_data
            ]
        );
        //print_r($this->resource_data);
        // load from array:
        $resource->fromArray($this->resource_data);

        $save = $resource->save();

        if ($save) {
            // TVs:
            foreach ($tvs as $tv_name => $value) {
                //$this->blender->out('  set TV: '.$tv_name.' '.$value);
                switch ($tv_name) {
                    case 'primaryCategory':
                        // no break
                    case 'optionalCategories':
                    case 'optonialCategories';
                        // no break
                    case 'rvor_author':
                        // no break
                        if (!empty($value)) {
                            $value = $this->blender->getResourceIDFromSeedKey($value);
                        }
                        break;
                    case 'blogit.post_main_image':
                        // no break;
                    case 'blogit.post_thumb_image':
                        // no break;
                    case 'rvor_author_pic':
                        $value = str_replace('/assets/content/rvor/', '', $value);
                        break;
                }
                $resource->setTVValue($tv_name, $value);
            }
            // extras
            $tagger = $this->blender->getTagger();
            if ($tagger instanceof \Tagger) {
                $this->setResourceTags($resource, (isset($extras['tagger']) ? $extras['tagger'] : []));
            }

            $this->modx->invokeEvent(
                'OnBlendResourceAfterSave',
                [
                    'blender' => $this->blender,
                    'blendResource' => $this,
                    'resource' => &$resource,
                    'tvs' => $tvs,
                    'extras' => $extras,
                    'data' => &$this->resource_data
                ]
            );
        }
        return $save;
    }

    /**
     * @param string $name
     * @param \modResource $resource
     *
     * @return int
     */
    protected function getTemplateIDFromName($name, $resource)
    {
        $id = 0;
        $this->modx->invokeEvent(
            'OnBlendSeedResource',
            [
                'blender' => $this->blender,
                'blendResource' => $this,
                'resource' => &$resource,
                'data' => &$this->resource_data
            ]
        );
        if ($name == 'Blogit post') {
            $name = 'Blog post';
        }
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
     * @return bool
     */
    public function isExists()
    {
        return $this->exists;
    }

    /**
     * @param string $seed_key
     *
     * @return bool|modResource
     */
    public function getResourceFromSeedKey($seed_key)
    {
        // get the alias:
        $alias = $this->blender->getAliasFromSeedKey($seed_key);

        return $this->modx->getObject('modResource', ['alias' => $alias]);
    }

    /**
     * @param \modResource $resource
     *
     * @return string
     */
    public function seedResource(\modResource $resource)
    {
        // No IDs! must get the alias and get a seed key,
        // @TODO need to log error and exit if any duplicate alias
        $seed_key = $this->blender->getSeedKeyFromAlias($resource->get('alias'));
        $this->resource_data = $resource->toArray();
        // get the par
        if ($this->resource_data['parent'] > 0 ) {
            $this->resource_data['parent'] = $this->blender->getResourceSeedKeyFromID($this->resource_data['parent']);
        }
        $this->resource_data['template'] = '';

        // no IDs only TV name
        $tvs = [];// TemplateVarResources modTemplateVarResource
        // get Template:
        $template = $resource->getOne('Template');
        if (is_object($template)) {

            $this->resource_data['template'] = $template->get('templatename');

            // get all TemplateValues
            // this way insures that all TVs have values/default not just what has been set/saved
            $tvTemplates = $template->getMany('TemplateVarTemplates');
            foreach ($tvTemplates as $tvTemplate) {
                $tv = $tvTemplate->getOne('TemplateVar');
                $tv_name = $tv->get('name');

                $tvs[$tv_name] = $resource->getTVValue($tv_name);
                switch ($tv_name) {
                    case 'primaryCategory':
                        // no break
                    case 'optionalCategories':
                    case 'optonialCategories';
                        // no break
                    case 'rvor_author':
                        // no break
                        if ($tvs[$tv_name] > 0) {
                            $tvs[$tv_name] = $this->blender->getResourceSeedKeyFromID($tvs[$tv_name]);
                        }
                        break;
                }
            }
        }

        $this->resource_data['tv'] = $tvs;

        $this->resource_data['extras'] = [];
        // tagger:
        $tagger = $this->blender->getTagger();
        if ($tagger instanceof \Tagger) {
            $this->resource_data['extras']['tagger'] = $this->getResourceTags($resource);
        }

        // https://docs.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.invokeevent
        $this->modx->invokeEvent(
            'OnBlendSeedResource',
            [
                'blender' => $this->blender,
                'blendResource' => $this,
                'resource' => &$resource,
                'data' => &$this->resource_data
            ]
        );

        // now cache it:
        $this->modx->cacheManager->set(
            $seed_key,
            $this->resource_data,
            $this->cache_life,
            $this->cacheOptions
        );

        return $seed_key;
    }

    /**
     * @param \modResource $resource
     *
     * @return array
     */
    protected function getResourceTags(\modResource $resource)
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

        $query->where(['Resources.resource' => $resource->get('id')]);

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