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
    protected $seeds_dir = '';

    /** @var int $cache_life in seconds, 0 is forever */
    protected $cache_life = 0;

    /** @var array  */
    protected $resource_data = [];

    /** @var string  */
    protected $context_key = 'web';

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

        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => 'resources',
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
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
        return $this;
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
     * @deprecated v0.9.7, use setSeedsDir
     * @param string $timestamp ~ will be the directory name
     *
     * @return $this
     */
    public function setSeedTimeDir($timestamp)
    {
        return $this->setSeedsDir($timestamp);
    }

    /**
     * @return string
     */
    public function getContextKey()
    {
        return $this->context_key;
    }

    /**
     * @param string $context_key
     * @return \LCI\Blend\Resource
     */
    public function setContextKey(string $context_key)
    {
        $this->context_key = $context_key;
        return $this;
    }

    // @TODO convenience methods to build resource custom pages not just from seeds


    /**
     * @param string $seed_key
     * @param string $context
     * @param bool $backup
     *
     * @return bool|array
     */
    protected function loadResourceDataFromSeed($seed_key, $context='web', $backup=false)
    {
        $this->resource_data = $this->getCacheData($seed_key, $context, $backup);
        return $this->resource_data;
    }

    /**
     * @param \modResource $resource
     * @param string $seed_key
     *
     * @return void
     */
    public function backupCurrentVersion($resource, $seed_key)
    {
        // save the raw data, this is for local only not portable
        $resource_data = $resource->toArray();

        // no IDs only TV name
        $tvs = [];// TemplateVarResources modTemplateVarResource

        $template = $resource->getOne('Template');
        if (is_object($template)) {

            // get all TemplateValues
            $tvTemplates = $template->getMany('TemplateVarTemplates');
            foreach ($tvTemplates as $tvTemplate) {
                $tv = $tvTemplate->getOne('TemplateVar');
                $tv_name = $tv->get('name');

                $tvs[$tv_name] = [
                    'type' => $tv->get('type'),
                    'value' => $resource->getTVValue($tv_name)
                ];
            }
        }

        $resource_data['tv'] = $tvs;

        // resource groups
        $resource_data['resource_groups'] = $resource->getResourceGroupNames();

        // now cache it
        $this->setCacheData($seed_key, $resource_data, $resource_data['context_key'], true);
    }

    public function blendFromSeed($seed_key, $overwrite=false)
    {
        $save = false;
        if (!$this->loadResourceDataFromSeed($seed_key, $this->context_key)) {
            $this->blender->out('Resource seed key: '.$seed_key. ' was not found in ' . $this->blender->getSeedsDirectory($this->seeds_dir));
            return false;
        }
        // does it exist
        // @TODO make way to change the alias and get data together
        $resource = $this->getResourceFromSeedKey($seed_key, $this->context_key);
        if ($resource) {
            $this->exists = true;
            if (!$overwrite) {
                $this->blender->out('   Current ID: '. $resource->get('id').' -- '. $resource->get('pagetitle'));
                $this->blender->out('   Seed ID: '. $this->resource_data['id'].' -- '. $this->resource_data['pagetitle']);
                return $save;
            }
            $this->backupCurrentVersion($resource, $seed_key);

        } else {
            $resource = $this->modx->newObject('modResource');
        }

        unset($this->resource_data['id']);

        $tvs = $this->resource_data['tv'];
        unset($this->resource_data['tv']);

        $extras = $this->resource_data['extras'];
        unset($this->resource_data['extras']);

        // get parent ID
        if (isset($this->resource_data['parent'])) {
            if (isset($this->resource_data['parent']['context']) && isset($this->resource_data['parent']['seed_key'])) {
                $this->resource_data['parent'] = $this->blender->getResourceIDFromSeedKey(
                    $this->resource_data['parent']['seed_key'],
                    $this->resource_data['parent']['context']
                );
            } else {
                // < v0.9.9
                $this->resource_data['parent'] = $this->blender->getResourceIDFromSeedKey($this->resource_data['parent']);
            }
        }

        // get template
        $this->resource_data['template'] = $this->getTemplateIDFromName($this->resource_data['template'], $resource);

        $this->modx->invokeEvent(
            'OnBlendResourceBeforeSave',
            [
                'blender' => $this->blender,
                'blendResource' => $this,
                'resource' => $resource,
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
            if (is_array($tvs) && count($tvs) > 0) {
                foreach ($tvs as $tv_name => $tv_data) {
                    //$this->blender->out('  set TV: '.$tv_name.' '.$value);
                    $value = $tv_data['value'];

                    switch ($tv_data['type']) {
                        case 'resourcelist':
                            if (isset($tv_data['portable_value']) && isset($tv_data['portable_value']['context']) && isset($tv_data['portable_value']['seed_keuy']) ) {
                                $value = $this->blender->getResourceIDFromSeedKey($tv_data['portable_value']['seed_key'], $tv_data['portable_value']['context']);

                            } elseif (isset($tv_data['portable_value'])) {
                                // < v0.9.9
                                $value = $this->blender->getResourceIDFromSeedKey($tv_data['portable_value']);
                            }
                            break;
                    }

                    $resource->setTVValue($tv_name, $value);
                }
            }

            $this->assignResourceGroups($resource, (isset($this->resource_data['resource_groups']) ? $this->resource_data['resource_groups'] : [] ));

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
                    'resource' => $resource,
                    'tvs' => $tvs,
                    'extras' => $extras,
                    'data' => &$this->resource_data
                ]
            );
        }
        return $save;
    }

    /**
     * @param string $seed_key
     *
     * @return bool
     */
    public function revertBlendFromSeed($seed_key)
    {
        $this->loadResourceDataFromSeed($seed_key, $this->context_key, true);
        // new-alias

        $resource = $this->getResourceFromSeedKey($seed_key, $this->context_key);

        // 1. get previous data from cache:
        $data = $this->getCacheData($seed_key, $this->context_key, true); $this->modx->cacheManager->get('down-'.$seed_key, $this->cacheOptions);

        if (!$data) {
            if ($this->isDebug()) {
                $this->blender->out('Remove resource: ' . $seed_key.'');
            }
            return $resource->remove();

        } elseif (is_array($data)) {
            if ($this->isDebug()) {
                $this->blender->out('Restore resource to old version ' . $seed_key);
            }
            // load old data:
            $resource->fromArray($data);
            $resource->save();

            // @TODO remove all current TV values?

            // tvs:
            foreach ($data['tv'] as $tv => $tv_data) {
                $resource->setTVValue($tv, $tv_data['value']);
            }

            $this->assignResourceGroups($resource, (isset($data['resource_groups']) ? $data['resource_groups'] : [] ));

            return $resource->save();
        }

        return false;
    }

    /**
     * @param \modResource $resource
     * @param array $new_groups
     */
    protected function assignResourceGroups($resource, $new_groups=[])
    {
        // resource groups
        $current_groups = $resource->getResourceGroupNames();
        foreach ($current_groups as $group) {
            if (!in_array($group, $new_groups)) {
                $resource->leaveGroup($group);
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
                $resource->joinGroup($group);
            }
        }
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
     * @param \modResource $resource
     * @param string $type
     *
     * @return string
     */
    public function seed(\modResource $resource, $type='export')
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

                $tvs[$tv_name] = [
                    'type' => $tv->get('type'),
                    'value' => $resource->getTVValue($tv_name)
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

        $this->resource_data['tv'] = $tvs;

        // resource groups
        $this->resource_data['resource_groups'] = $resource->getResourceGroupNames();

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
                'data' => &$this->resource_data,
                'type' => $type
            ]
        );

        // now cache it:
        $this->modx->cacheManager->set(
            ($type == 'backup' ? 'down-' : '').$seed_key,
            $this->resource_data,
            $this->cache_life,
            $this->cacheOptions
        );

        return $seed_key;
    }

    /**
     * @param array $resource_data
     *
     * @return $this
     */
    public function setResourceData(array $resource_data)
    {
        $this->resource_data = $resource_data;
        return $this;
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

    /**
     * @param $seed_key
     * @param $context
     * @param bool $backup
     * @return mixed
     */
    protected function getCacheData($seed_key, $context, $backup=false)
    {
        $cache_options = $this->cacheOptions;
        $cache_options[\xPDO::OPT_CACHE_KEY] .= '/'.$context;//'resources'

        $key = $seed_key;
        if ($backup) {
            $key = 'down-' . $seed_key;
        }

        return $this->modx->cacheManager->get($key, $cache_options);
    }

    /**
     * @param $seed_key
     * @param $data
     * @param $context
     * @param bool $backup
     */
    protected function setCacheData($seed_key, $data, $context, $backup=false)
    {
        $cache_options = $this->cacheOptions;
        $cache_options[\xPDO::OPT_CACHE_KEY] .= '/'.$context;//'resources'

        $key = $seed_key;
        if ($backup) {
            $key = 'down-' . $seed_key;
        }
        // now cache it
        $this->modx->cacheManager->set(
            $key,
            $data,
            $this->cache_life,
            $cache_options
        );
    }
}
