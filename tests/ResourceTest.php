<?php
//declare(strict_types=1);

final class ResourceTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    protected $parent_alias = 'blendable-resource';

    protected $template_name = 'resourceTemplate';


    public function testCreateTemplateWithTVs()
    {
        // 1. Create TVs
        $this->makeTVs();

        // 2. Create Template and attach to Template
        $this->makeTemplate();

        $template = $this->modx->getObject('modTemplate', ['templatename' => $this->template_name]);

        $this->assertInstanceOf(
            '\modTemplate',
            $template,
            'Did template get created'
        );

        $this->assertEquals(
            $this->template_name,
            $template->get('templatename'),
            'Compare template name'
        );

        // TVs:
        $created_tvs = [];
        $tvTemplates = $template->getMany('TemplateVarTemplates');
        /** @var modTemplateVarTemplate $tvTemplate */
        foreach ($tvTemplates as $tvTemplate) {
            $tv = $tvTemplate->getOne('TemplateVar');
            $created_tvs[] = $tv->get('name');
        }

        $tvs = [];
        foreach ($this->tvs as $name => $info) {
            $tvs[] = $name;
        }

        sort($created_tvs);
        sort($tvs);

        $this->assertEquals(
            $tvs,
            $created_tvs,
            'Compare the created TVs'
        );
    }

    /**
     * @depends testCreateTemplateWithTVs
     */
    public function testGetBlendableResource()
    {
        //$this->modx->loadClass('sources.modMediaSource');

        $alias = $this->parent_alias;
        $content = 'Content, can put in HTML here';
        $description = 'This is description, don\'t put in HTML here';
        $long_title = 'Long title';
        $page_title = 'Page Title';
        $rich_text_tv = '<h2>This is only a test</h2>';
        $text_area_tv = 'Lots of lines can go here ' . PHP_EOL . 'Line 2';
        $text_tv = 'A single line value goes here';

        /** @var \LCI\Blend\Blendable\Resource $blendableResource */
        $blendableResource = $this->blender->getBlendableLoader()->getBlendableResource($alias);
        $blendableResource
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldContent($content)
            ->setFieldDescription($description)
            ->setFieldLongtitle($long_title)
            ->setFieldPagetitle($page_title)
            ->setFieldTemplate($this->template_name)
            ->setTVValue('richTextTV', $rich_text_tv)
            ->setTVValue('textTV', $text_tv)
            ->setTVValue('textAreaTV', $text_area_tv);

        $blended = $blendableResource->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $alias.' resource blend attempted'
        );

        // Validate data/convenience methods:
        if ($blended) {
            /** @var \LCI\Blend\Blendable\Resource $blendResource */
            $blendResource = $this->blender->getBlendableLoader()->getBlendableResource($alias);
            $this->assertInstanceOf(
                '\LCI\Blend\Blendable\Resource',
                $blendResource,
                'Validate instance was created \LCI\Blend\Blendable\Resource'
            );

            $this->assertEquals(
                $alias,
                $blendResource->getFieldAlias(),
                'Compare resource alias'
            );

            $this->assertEquals(
                $content,
                $blendResource->getFieldContent(),
                'Compare content'
            );

            $this->assertEquals(
                $description,
                $blendResource->getFieldDescription(),
                'Compare description'
            );

            $this->assertEquals(
                $long_title,
                $blendResource->getFieldLongtitle(),
                'Compare long title'
            );

            $this->assertEquals(
                $page_title,
                $blendResource->getFieldPagetitle(),
                'Compare page title'
            );

            /** @var modResource $modResource */
            $modResource = $blendableResource->getXPDOSimpleObject();

            $this->assertEquals(
                $this->template_name,
                $blendableResource->getFieldTemplate(),
                'Assigning resource to template failed'
            );

            $this->assertEquals(
                $rich_text_tv,
                $modResource->getTVValue('richTextTV'),
                'TV richTextTV failed'
            );

            $this->assertEquals(
                $text_tv,
                $modResource->getTVValue('textTV'),
                'TV textTV failed'
            );

            $this->assertEquals(
                $text_area_tv,
                $modResource->getTVValue('textAreaTV'),
                'TV textAreaTV failed'
            );

        }
    }

    /**
     * @depends testGetBlendableResource
     */
    public function testGetBlendableResourceChild()
    {
        //$this->modx->loadClass('sources.modMediaSource');

        $alias = 'blendable-resource-child';
        $content = 'Content, can put in HTML here';
        $description = 'This is description, don\'t put in HTML here';
        $long_title = 'Child Long title';
        $page_title = 'Child Page Title';
        $rich_text_tv = '<h2>Children, this is only a test</h2>';
        $text_area_tv = 'Lots of lines can go here ' . PHP_EOL . 'Line 2';
        $text_tv = 'A single line value goes here';

        /** @var \LCI\Blend\Blendable\Resource $blendableResource */
        $blendableResource = $this->blender->getBlendableLoader()->getBlendableResource($alias);
        $blendableResource
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldContent($content)
            ->setFieldDescription($description)
            ->setFieldLongtitle($long_title)
            ->setFieldPagetitle($page_title)
            ->setFieldTemplate($this->template_name)
            ->setFieldParentFromAlias($this->parent_alias, 'web')
            ->setTVValueResourceIDFromAlias('resourceListTV', $this->parent_alias, 'web')
            ->setTVValue('richTextTV', $rich_text_tv)
            ->setTVValue('textTV', $text_tv)
            ->setTVValue('textAreaTV', $text_area_tv);

        $blended = $blendableResource->blend(true);
        $this->assertEquals(
            true,
            $blended,
            $alias.' resource blend attempted'
        );

        // Validate data/convenience methods:
        if ($blended) {
            /** @var \LCI\Blend\Blendable\Resource $blendResource */
            $blendResource = $this->blender->getBlendableLoader()->getBlendableResource($alias);
            $this->assertInstanceOf(
                '\LCI\Blend\Blendable\Resource',
                $blendResource,
                'Validate instance was created \LCI\Blend\Blendable\Resource'
            );

            $this->assertEquals(
                $alias,
                $blendResource->getFieldAlias(),
                'Compare resource alias'
            );

            $this->assertEquals(
                $content,
                $blendResource->getFieldContent(),
                'Compare content'
            );

            $this->assertEquals(
                $description,
                $blendResource->getFieldDescription(),
                'Compare description'
            );

            $this->assertEquals(
                $long_title,
                $blendResource->getFieldLongtitle(),
                'Compare long title'
            );

            $this->assertEquals(
                $page_title,
                $blendResource->getFieldPagetitle(),
                'Compare page title'
            );

            /** @var modResource $parentResource */
            $parentResource = $this->modx->getObject('modResource', ['alias' => $this->parent_alias]);

            /** @var modResource $modResource */
            $modResource = $blendableResource->getXPDOSimpleObject();

            $this->assertEquals(
                $parentResource->get('id'),
                $modResource->get('parent'),
                'Assigning resource to parent failed'
            );

            $this->assertEquals(
                $this->template_name,
                $blendableResource->getFieldTemplate(),
                'Assigning resource to template failed'
            );

            $this->assertEquals(
                $rich_text_tv,
                $modResource->getTVValue('richTextTV'),
                'TV richTextTV failed'
            );

            $this->assertEquals(
                $text_tv,
                $modResource->getTVValue('textTV'),
                'TV textTV failed'
            );

            $this->assertEquals(
                $text_area_tv,
                $modResource->getTVValue('textAreaTV'),
                'TV textAreaTV failed'
            );

            $this->assertEquals(
                $parentResource->get('id'),
                $modResource->getTVValue('resourceListTV'),
                'Assigning resourceListTV to resource ID failed'
            );

        }
    }

    /**
     * @depends testGetBlendableResource
     */
    public function testRevertGetBlendableResource()
    {
        //$this->modx->loadClass('sources.modMediaSource');

        $alias = 'blendable-resource';

        /** @var \LCI\Blend\Blendable\Resource $blendableResource */
        $blendableResource = $this->blender->getBlendableLoader()->getBlendableResource($alias);
        $blendableResource->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $this->assertEquals(
            true,
            $blendableResource->revertBlend(),
            $alias.' resource revertBlend attempted'
        );

        // remove template:
        $template = $this->blender->getBlendableLoader()->getBlendableTemplate($this->template_name);
        $this->assertEquals(
            true,
            $template->delete(),
            $this->template_name.' template was attempted to be deleted'
        );

        // remove TVs:
        foreach ($this->tvs as $name => $info) {
            $tv = $this->blender->getBlendableLoader()->getBlendableTemplateVariable($name);
            if (is_object($tv->getXPDOSimpleObject()) ) {
                $this->assertEquals(
                    true,
                    $tv->delete(),
                    $name . ' TV was attempted to be deleted'
                );
            }
        }
    }


    public function testBlendManyResources()
    {
        $resource_seeds = [
            'web' => [
                'test-blend-many-resource-1',
                'test-blend-many-resource-2'
            ]
        ];

        $this->assertEquals(
            true,
            $this->blender->getBlendableLoader()->blendManyResources($resource_seeds, BLEND_TEST_SEEDS_DIR, true),
            'testBlendManyResources() blend attempted'
        );

        // verify that they exist:
        foreach ($resource_seeds as $context => $seeds) {
            foreach ($seeds as $seed) {
                $testResource = $this->modx->getObject('modResource', ['alias' => $seed]);
                $this->assertInstanceOf(
                    '\modResource',
                    $testResource,
                    'Verifying that ' . $seed . ' was created'
                );

                // Resource groups:
                $this->assertEquals(
                    true,
                    $testResource->isMember('Test Resource Group'),
                    'Verifying that ' . $seed . ' was attached to a resource group'
                );

            }
        }
    }

    /**
     * @depends testBlendManyResources
     */
    public function testRevertBlendManyResources()
    {
        $resource_seeds = [
            'web' => [
                'test-blend-many-resource-1',
                'test-blend-many-resource-2'
            ]
        ];

        $this->assertEquals(
            true,
            $this->blender->getBlendableLoader()->revertBlendManyResources($resource_seeds, BLEND_TEST_SEEDS_DIR),
            'testBlendManyResources() blend attempted'
        );

        // verify that they exist:
        foreach ($resource_seeds as $context => $seeds ) {
            foreach ($seeds as $seed) {
                $testResource = $this->modx->getObject('modResource', ['alias' => $seed, 'context_key' => $context]);
                $this->assertEquals(
                    false,
                    $testResource,
                    'Verifying that ' . $seed . ' was removed/reverted'
                );
            }
        }
    }

    /**
     * @depends testBlendManyResources
     */
    public function testMakeResourceSeeds()
    {
        $test_resources = [
            'test-blend-many-resource-3' => [
                'pagetitle' => 'Blend Resource Test 3',
                'content' => 'Content #3 goes here'
            ],
            'test-blend-many-resource-4' => [
                'pagetitle' => 'Blend Resource Test 4',
                'content' => 'Content #4 goes here'
            ],
        ];

        $aliases = [];
        foreach ($test_resources as $alias => $test_resource) {
            // Make test resource:
            $testResource2 = $this->modx->getObject('modResource', ['alias' => $alias]);
            if (is_object($testResource2)) {
                $this->assertEquals(
                    false,
                    $testResource2,
                    $alias . ' resource already exists'
                );

            } else {
                $aliases[] = $alias;

                $testResource2 = $this->modx->newObject('modResource');
                $testResource2->fromArray($test_resource);
                $testResource2->set('alias', $alias);
                $testResource2->save();
            }
        }

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seeds_directory = $this->blender->getMigrationName('resource');

        $seeds = $this->blender->getSeedMaker()->makeResourceSeeds(['alias:IN' => $aliases]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.'testResource2.migration.php'))),
            $this->removeStringLineEndings($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().'m2018_01_10_093000_Resource.php'))),
            'Comparing existing testResource2 migration file with generated file'
        );

        $count = 3;
        foreach ($test_resources as $alias => $test_resource) {
            $fixed_data = require_once BLEND_COMPARE_DIRECTORY . 'testResource'.$count.'.seed.php';
            $generated_data = false;
            $seed_file = $this->blender->getSeedsPath($seeds_directory) . 'resources' . DIRECTORY_SEPARATOR . 'web'. DIRECTORY_SEPARATOR . $alias.'.cache.php';
            if (file_exists($seed_file)) {
                $generated_data = require_once $seed_file;
            }
            unset($generated_data['columns']['id'], $generated_data['columns']['createdon'], $generated_data['columns']['alias_visible']);

            $this->assertEquals(
                $fixed_data,
                $generated_data,
                'Comparing existing testResource'.$count++.' seed file with generated seed file: '.$seed_file.PHP_EOL
            );
        }

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testMakeResourceSeeds
     */
    public function testCleanUpMakeResourceSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $aliases = [
            'test-blend-many-resource-3',
            'test-blend-many-resource-4'
        ];

        if (BLEND_CLEAN_UP) {
            // Remove created test resources:
            $testResources = $this->modx->getCollection('modResource', ['alias:IN' => $aliases]);
            /** @var modResource $testResource */
            foreach ($testResources as $testResource) {
                $testResource->remove();
            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'resource'),
                'Remove created resource2 migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }

    public function testRemoveBlend()
    {
        if (BLEND_CLEAN_UP) {
            $this->blender->install('down');

            $this->assertEquals(
                false,
                $this->blender->isBlendInstalledInModx()
            );
        }
    }

    protected $tvs = [
        // Plan test, resource, media source and template
        'mediaSourceTV' => [
            'description' => 'A MODX MediaSource ID',
            'caption' => 'Some caption',
            'type' => 'text',
            'display' => 'default',
            'default_text' => ''
        ],
        'resourceListTV' => [
            'description' => 'A MODX Resource ID',
            'caption' => 'Some caption',
            'type' => 'resourcelist',
            'display' => 'default',
            'default_text' => ''
        ],
        'richTextTV' =>[
            'description' => 'Describe the event here.',
            'caption' => 'Description',
            'type' => 'richtext',
            'display' => 'default',
            'default_text' => ''
        ],
        'textTV' =>[
            'description' => 'Text here.',
            'caption' => 'Text',
            'type' => 'text',
            'display' => 'default',
            'default_text' => ''
        ],
        'textAreaTV' =>[
            'description' => 'Describe the event here.',
            'caption' => 'Description',
            'type' => 'textarea',
            'display' => 'default',
            'default_text' => ''
        ]
    ];

    protected function makeTVs()
    {
        foreach ($this->tvs as $name => $info) {
            $blendableTV = $this->blender->getBlendableLoader()->getBlendableTemplateVariable($name);

            $blendableTV
                ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
                ->setFieldCategory('Tests=>Resource')
                ->setFieldCaption($info['caption'])
                ->setFieldDefaultText($info['default_text'])
                ->setFieldDescription($info['description'])
                ->setFieldDisplay($info['display'])
                ->setFieldType($info['type']);

            if ($blendableTV->blend(true)) {
                $this->blender->out($blendableTV->getFieldName() . ' was saved correctly');

            } else {
                //error
                $this->blender->outError($blendableTV->getFieldName() . ' did not save correctly ');
                $this->blender->outError(print_r($blendableTV->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
            }
        }
    }

    protected function makeTemplate()
    {
        /** @var \LCI\Blend\Blendable\Template $testTemplate1 */
        $testTemplate1 = $this->blender->getBlendableLoader()->getBlendableTemplate($this->template_name);
        $testTemplate1
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldDescription('ResourceTest')
            ->setFieldCategory('Tests=>Resource')
            ->setFieldCode('<html>ResourceTest</html>', true);

        foreach ($this->tvs as $name => $info) {
            $testTemplate1->attachTemplateVariable($name);
        }

        if ($testTemplate1->blend(true)) {
            $this->blender->out($testTemplate1->getFieldName() . ' was saved correctly');

        } else {
            //error
            $this->blender->outError($testTemplate1->getFieldName() . ' did not save correctly ');
            $this->blender->outError(print_r($testTemplate1->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }
    }
}
