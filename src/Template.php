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

    protected $remove_tv_names = [];

    protected $icon = '';

    protected $template_type = 0;

    /** @var string ~ the xPDO class name */
    protected $element_class = 'modTemplate';

    /** @var string */
    protected $name_column_name = 'templatename';

    /**
     * @param string $name
     *
     * @return Template
     */
    public function loadCurrentVersion($name)
    {
        /** @var Template $element */
        $element = new self($this->modx, $this->blender);
        $element->setSeedTimeDir($this->getTimestamp());
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
            'tv_name' => $tv_name,
            'rank' => $rank
        ];
        return $this;
    }
    
    protected function relatedPieces()
    {
        if (count($this->tv_names) > 0) {
            $tvs = [];
            foreach ($this->tv_names as $tv_name_data) {
                // get the TV:
                $tv = $this->modx->getObject('modTemplateVariable', ['name' => $tv_name_data['name']]);
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
        // @TODO remove any related that are not in the seed
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
                ->setSeedTimeDir($this->getTimestamp())
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
        foreach ($tvs as $tv) {
            // seed the TV:
            $tvSeed = new TemplateVariable($this->modx, $this->blender);
            $tvSeed
                ->setSeedTimeDir($this->getTimestamp())
                ->loadElementDataFromSeed($tv['seed_key']);
            $tvSeed->save(true);

            $this->attachTemplateVariable($tv['name'], $tv['rank']);
        }
    }
}
