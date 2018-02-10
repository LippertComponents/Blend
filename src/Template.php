<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 9/30/2017
 * Time: 2:44 PM
 */

namespace LCI\Blend;


class Template extends Element
{
    protected $tv_names = [];

    /** @var array  */
    protected $detach_tvs = [];

    protected $remove_tv_names = [];

    protected $icon = '';

    protected $template_type = 0;

    /** @var string ~ the xPDO class name */
    protected $element_class = 'modTemplate';

    /** @var string */
    protected $name_column_name = 'templatename';

    /** @var array  */
    protected $tv_seeds = [];

    /**
     * @param string $name
     *
     * @return Template
     */
    public function loadCurrentVersion($name)
    {
        /** @var Template $element */
        $element = new self($this->modx, $this->blender);
        $element->setSeedsDir($this->getSeedsDir());
        return $element->loadElementFromName($name);
    }

    /**
     * @param string $tv_name
     * @param int $rank
     *
     * @return $this
     */
    public function attachTemplateVariable($tv_name, $rank=0)
    {
        $this->tv_names[] = [
            'name' => $tv_name,
            'rank' => $rank
        ];
        return $this;
    }

    /**
     * @param $tv_name
     * @return $this
     */
    public function detachTV($tv_name)
    {
        $this->detach_tvs[] = $tv_name;
        return $this;
    }

    protected function relatedPieces()
    {
        foreach ($this->tv_seeds as $tv) {
            // seed the TV:
            $tvSeed = new TemplateVariable($this->modx, $this->blender);
            $tvSeed
                ->setSeedsDir($this->getSeedsDir())
                ->loadElementDataFromSeed($tv['seed_key']);
            $tvSeed->blend(true);

            $this->attachTemplateVariable($tv['name'], $tv['rank']);
        }

        if (count($this->tv_names) > 0) {
            $tvs = [];
            foreach ($this->tv_names as $tv_name_data) {
                // get the TV:
                $tv = $this->modx->getObject('modTemplateVar', ['name' => $tv_name_data['name']]);
                if ($tv) {
                    $tvt = $this->modx->newObject('modTemplateVarTemplate');
                    $tvt->set('tmplvarid', $tv->get('id'));
                    $tvt->set('rand', $tv_name_data['rank']);

                    $tvs[] = $tvt;
                } else {
                    $this->error = true;

                }

            }
            $this->element->addMany($tvs, 'TemplateVarTemplates');
        }
    }

    protected function relatedPiecesAfterSave()
    {
        if (count($this->detach_tvs) > 0) {
            $tvs = [];
            foreach ($this->tv_names as $tv_name) {
                // get the TV:
                $tv = $this->modx->getObject('modTemplateVar', ['name' => $tv_name]);
                if ($tv) {
                    $templateVarTemplate = $this->modx->getObject('modTemplateVarTemplate', array(
                        'tmplvarid' => $tv->get('id'),
                        'templateid' => $this->element->get('id'),
                    ));
                    if ($templateVarTemplate && $templateVarTemplate instanceof modTemplateVarTemplate) {
                        $templateVarTemplate->remove();
                    }
                }
            }
        }
    }

    /**
     * @param array|bool $data ~ the data loaded from the down seed
     */
    protected function revertRelatedPieces($data)
    {
        $this->tv_names;
        $revert_tvs = [];
        if (is_array($data) && isset($data['tvs'])) {
            $revert_tvs = $data['tvs'];
        }
        foreach ($this->tv_seeds as $tv) {
            // seed the TV:
            $tvSeed = new TemplateVariable($this->modx, $this->blender);
            $tvSeed
                ->setSeedsDir($this->getSeedsDir())
                ->loadElementDataFromSeed($tv['seed_key']);
            $tvSeed->revertBlend();

            if (is_array($data) && !in_array($tv['seed_key'], $revert_tvs)) {
                $this->detachTV($tv['seed_key']);
            }
        }

        if (is_array($data)) {
            $this->tv_seeds = $revert_tvs;
            $this->relatedPieces();
            $this->element->save();
            $this->relatedPiecesAfterSave();
        }
    }

    protected function setAdditionalElementColumns()
    {
        $this->element->save();
        $this->element->set('icon', $this->icon);
        $this->element->set('template_type', $this->template_type);
    }

    /**
     * @param \modTemplate $template
     *
     * @return \modTemplate
     */
    protected function seedRelated($template)
    {
        // get all related TVs:
        $tv_keys = [];
        $tvTemplates = $template->getMany('TemplateVarTemplates');
        foreach ($tvTemplates as $tvTemplate) {
            $tv = $tvTemplate->getOne('TemplateVar');
            $tv_name = $tv->get('name');

            $tvSeed = new TemplateVariable($this->modx, $this->blender);
            $seed_key = $tvSeed
                ->setSeedsDir($this->getSeedsDir())
                ->seedElement($tv);
            $tv_keys[] = [
                'seed_key' => $seed_key,
                'name' => $tv_name,
                'rank' => $tvTemplate->get('rank')
            ];
            $this->blender->out('TV '.$tv_name. ' has been seeded: '.$seed_key);
        }
        $this->element_data['tvs'] = $tv_keys;

        return $template;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setTemplateName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param array $tvs
     */
    public function setTvs($tvs)
    {
        $this->tv_seeds = $tvs;
    }
}
