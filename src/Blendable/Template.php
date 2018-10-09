<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 2:44 PM
 */

namespace LCI\Blend\Blendable;


use SebastianBergmann\CodeCoverage\Report\PHP;

class Template extends Element
{
    /** @var string  */
    protected $opt_cache_key = 'elements/templates';

    /** @var string ~ the xPDO class name */
    protected $xpdo_simple_object_class = 'modTemplate';

    /** @var string  */
    protected $unique_key_column = 'templatename';

    /** @var array  */
    protected $tv_names = [];

    /** @var array  */
    protected $detach_tvs = [];

    /** @var array  */
    protected $tv_seeds = [];

    /**
     * @return \LCI\Blend\Blendable\Template
     */
    public function getCurrentVersion()
    {
        /** @var \LCI\Blend\Blendable\Template $element */
        $element = new self($this->modx, $this->blender, $this->getFieldName());
        return $element->setSeedsDir($this->getSeedsDir());
    }

    /**
     * @return string
     */
    public function getFieldIcon()
    {
        return $this->blendable_xpdo_simple_object_data['icon'];
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->getFieldTemplateName();
    }

    /**
     * @return string
     */
    public function getFieldTemplateName()
    {
        return $this->blendable_xpdo_simple_object_data['templatename'];
    }

    /**
     * @return string
     */
    public function getFieldTemplateType()
    {
        return $this->blendable_xpdo_simple_object_data['template_type'];
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setFieldIcon($icon)
    {
        $this->blendable_xpdo_simple_object_data['icon'] = $icon;
        return $this;
    }
    /**
     * @param string $name
     * @return $this
     */
    public function setFieldName($name)
    {
        return $this->setFieldTemplateName($name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setFieldTemplateName($name)
    {
        $this->blendable_xpdo_simple_object_data['templatename'] = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setFieldTemplateType($name)
    {
        $this->blendable_xpdo_simple_object_data['template_type'] = $name;
        return $this;
    }

    /**
     * @param array $tvs
     */
    public function setTvs($tvs)
    {
        $this->tv_seeds = $tvs;
    }

    /**
     * @param string $tv_name
     * @param int $rank
     *
     * @return $this
     */
    public function attachTemplateVariable($tv_name, $rank = 0)
    {
        if (!isset($this->related_data['attach'])) {
            $this->related_data['attach'] = [];
        }
        $this->related_data['attach'][] = [
            'name' => $tv_name,
            'rank' => $rank
        ];
        return $this;
    }

    /**
     * @param $tv_name
     * @return $this
     */
    public function detachTemplateVariable($tv_name)
    {
        if (!isset($this->related_data['detach'])) {
            $this->related_data['detach'] = [];
        }
        $this->related_data['detach'][] = $tv_name;
        return $this;
    }

    /**
     * @deprecated use detachTemplateVariable
     * @param $tv_name
     * @return $this
     */
    public function detachTV($tv_name)
    {
        return $this->detachTemplateVariable($tv_name);
    }

    protected function attachRelatedPieces()
    {
        if (isset($this->related_data['seeds'])) {
            foreach ($this->related_data['seeds'] as $tv) {
                // blend the the TV from seed:
                $tvSeed = new TemplateVariable($this->modx, $this->blender, $tv['name']);
                $tvSeed
                    ->setSeedsDir($this->getSeedsDir())
                    ->blendFromSeed($tv['seed_key'], true);

                $this->attachTemplateVariable($tv['name'], $tv['rank']);
            }
        }

        if (isset($this->related_data['attach']) && count($this->related_data['attach']) > 0) {
            $tvs = [];
            foreach ($this->related_data['attach'] as $tv_name_data) {
                // get the TV:
                $tv = $this->modx->getObject('modTemplateVar', ['name' => $tv_name_data['name']]);
                if ($tv) {
                    $tvt = $this->modx->getObject('modTemplateVarTemplate', ['tmplvarid' => $tv->get('id'), 'templateid' => $this->xPDOSimpleObject->getPrimaryKey()]);

                    if (!$tvt) {
                        $tvt = $this->modx->newObject('modTemplateVarTemplate');
                    }
                    $tvt->set('tmplvarid', $tv->get('id'));
                    $tvt->set('rand', $tv_name_data['rank']);

                    $tvs[] = $tvt;
                } else {
                    $this->error = true;

                }

            }
            $this->xPDOSimpleObject->addMany($tvs, 'TemplateVarTemplates');
        }
    }

    protected function attachRelatedPiecesAfterSave()
    {
        if (isset($this->related_data['detach']) && count($this->related_data['detach']) > 0) {
            foreach ($this->related_data['detach'] as $tv_name) {
                // get the TV:
                $tv = $this->modx->getObject('modTemplateVar', ['name' => $tv_name]);
                if ($tv) {
                    $templateVarTemplate = $this->modx->getObject('modTemplateVarTemplate', array(
                        'tmplvarid' => $tv->get('id'),
                        'templateid' => $this->xPDOSimpleObject->get('id'),
                    ));
                    if ($templateVarTemplate && $templateVarTemplate instanceof \modTemplateVarTemplate) {
                        $templateVarTemplate->remove();
                    }
                }
            }
        }
    }

    /**
     *
     */
    protected function onDeleteRevertRelatedPieces()
    {
        if (!isset($this->related_data['seeds']) || empty($this->related_data['seeds'])) {
            // If this is a seed then the TVs are not in the revert file but the seed file
            $name = $this->getFieldTemplateName();
            if (empty ($name) && isset($this->current_xpdo_simple_object_data['templatename'])) {
                $name = $this->current_xpdo_simple_object_data['templatename'];
            }
            $this->loadObjectDataFromSeed($this->blender->getSeedKeyFromName($name));
        }

        if (isset($this->related_data['seeds'])) {

            foreach ($this->related_data['seeds'] as $tv) {
                // seed the TV:
                $tvSeed = new TemplateVariable($this->modx, $this->blender, $tv['name']);
                $tvSeed
                    ->setSeedsDir($this->getSeedsDir())
                    ->revertBlend();
            }
        }
    }

    /**
     * @var string $type blend or revert
     */
    protected function seedRelated($type = 'blend')
    {
        // get all related TVs:
        $tv_keys = [];
        if (is_object($this->xPDOSimpleObject)) {
            $tvTemplates = $this->xPDOSimpleObject->getMany('TemplateVarTemplates');
            /** @var \modTemplateVarTemplate $tvTemplate */
            foreach ($tvTemplates as $tvTemplate) {
                /** @var \modTemplateVar $tv */
                $tv = $tvTemplate->getOne('TemplateVar');
                $tv_name = $tv->get('name');

                $tvSeed = new TemplateVariable($this->modx, $this->blender, $tv_name);
                $seed_key = $tvSeed
                    ->setSeedsDir($this->getSeedsDir())
                    ->seed($this->type == 'revert' ? 'revert' : 'seed');

                $tv_keys[] = [
                    'seed_key' => $seed_key,
                    'name' => $tv_name,
                    'rank' => $tvTemplate->get('rank')
                ];
                $this->blender->out('TV '.$tv_name.' has been seeded: '.$seed_key);
            }

            $this->related_data['seeds'] = $tv_keys;

        } elseif ($type != 'revert') {
            $this->related_data['seeds'] = $tv_keys;
        }

    }
}
