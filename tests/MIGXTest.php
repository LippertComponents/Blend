<?php
//declare(strict_types=1);

final class MIGXTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = false;

    public function testField()
    {
        /** @var \LCI\Blend\Helpers\MIGX\Field $field */
        $field = new \LCI\Blend\Helpers\MIGX\Field('my_tv_field');
        $this->setFieldData($field);

        $this->assertEquals(
            [
                'field' => 'my_tv_field',
                'caption' => 'My Field',
                'description' => 'Fill in...',
                'inputTVtype' => 'listbox',
                'inputOptionValues' => 'Option 1==1||Option 2==Option 2||Other==z'
            ],
            $field->toArray(),
            'Compare Field toArray()'
        );

        $this->assertEquals(
            [
                'header' => 'Grid Column Header',
                'dataIndex' => 'my_tv_field',
                'width' => 200,
                'sortable' => true,
                'editor' => 'this.textEditor',
                'renderer' => 'this.renderImage'
            ],
            $field->getGridArray(),
            'Compare Field getGridArray()'
        );

    }

    /**
     * @depends testField
     */
    public function testTab()
    {
        $tab = new \LCI\Blend\Helpers\MIGX\Tab('Tab Caption');

        /** @var \LCI\Blend\Helpers\MIGX\Field $field */
        $field = $tab->makeField('field_one');
        $this->setFieldData($field);

        $field2 = $tab->makeField('field_two');
        $this->setFieldData($field2);

        $this->assertEquals(
            [
                'caption' => 'Tab Caption',
                'fields' => [
                    [
                        'field' => 'field_one',
                        'caption' => 'My Field',
                        'description' => 'Fill in...',
                        'inputTVtype' => 'listbox',
                        'inputOptionValues' => 'Option 1==1||Option 2==Option 2||Other==z'
                    ],
                    [
                        'field' => 'field_two',
                        'caption' => 'My Field',
                        'description' => 'Fill in...',
                        'inputTVtype' => 'listbox',
                        'inputOptionValues' => 'Option 1==1||Option 2==Option 2||Other==z'
                    ],
                ]
            ],
            $tab->toArray(),
            'Compare Tab->toArray()'
        );

        $this->assertEquals(
            [
                [
                    'header' => 'Grid Column Header',
                    'dataIndex' => 'field_one',
                    'width' => 200,
                    'sortable' => true,
                    'editor' => 'this.textEditor',
                    'renderer' => 'this.renderImage'
                ],
                [
                    'header' => 'Grid Column Header',
                    'dataIndex' => 'field_two',
                    'width' => 200,
                    'sortable' => true,
                    'editor' => 'this.textEditor',
                    'renderer' => 'this.renderImage'
                ]
            ],
            $tab->getGridColumns(),
            'Compare Tab->getGridColumns()'
        );
    }

    /**
     * @depends testTab
     */
    public function testMIGXTemplateVariableInput()
    {
        /** @var \LCI\Blend\Helpers\MIGXTemplateVariableInput $migxTemplateVariableInput */
        $migxTemplateVariableInput = new \LCI\Blend\Helpers\MIGXTemplateVariableInput();

        $migxTemplateVariableInput
            ->setAllowBlank(false)
            ->setAddButtonText('Add New Item')
            ->setConfigs('What is this for?')
            ->setAutoResourceFolders(true)
            ->setJsonVarKey('some-key');

        /** @var \LCI\Blend\Helpers\MIGX\Tab $tab1 */
        $tab1 = $migxTemplateVariableInput->addFormTab('Tab One');

        /** @var \LCI\Blend\Helpers\MIGX\Field $field */
        $tab1Field = $tab1->makeField('tab_one_field_one');
        $this->setFieldData($tab1Field);

        $tab1Field2 = $tab1->makeField('tab_one_field_two');
        $this->setFieldData($tab1Field2);

        /** @var \LCI\Blend\Helpers\MIGX\Tab $tab1 */
        $tab2 = $migxTemplateVariableInput->addFormTab('Tab Two');

        /** @var \LCI\Blend\Helpers\MIGX\Field $field */
        $tab2Field = $tab2->makeField('tab_two_field_one');
        $this->setFieldData($tab2Field);

        $tab2Field2 = $tab2->makeField('tab_two_field_two');
        $this->setFieldData($tab2Field2);

        $this->assertEquals(
            [
                'allowBlank' => false,
                'configs' => 'What is this for?',
                'formtabs' => '[
    {
        "caption": "Tab One",
        "fields": [
            {
                "field": "tab_one_field_one",
                "caption": "My Field",
                "description": "Fill in...",
                "inputTVtype": "listbox",
                "inputOptionValues": "Option 1==1||Option 2==Option 2||Other==z"
            },
            {
                "field": "tab_one_field_two",
                "caption": "My Field",
                "description": "Fill in...",
                "inputTVtype": "listbox",
                "inputOptionValues": "Option 1==1||Option 2==Option 2||Other==z"
            }
        ]
    },
    {
        "caption": "Tab Two",
        "fields": [
            {
                "field": "tab_two_field_one",
                "caption": "My Field",
                "description": "Fill in...",
                "inputTVtype": "listbox",
                "inputOptionValues": "Option 1==1||Option 2==Option 2||Other==z"
            },
            {
                "field": "tab_two_field_two",
                "caption": "My Field",
                "description": "Fill in...",
                "inputTVtype": "listbox",
                "inputOptionValues": "Option 1==1||Option 2==Option 2||Other==z"
            }
        ]
    }
]',
                'columns' => '[
    {
        "header": "Grid Column Header",
        "dataIndex": "tab_one_field_one",
        "width": 200,
        "sortable": true,
        "editor": "this.textEditor",
        "renderer": "this.renderImage"
    },
    {
        "header": "Grid Column Header",
        "dataIndex": "tab_one_field_two",
        "width": 200,
        "sortable": true,
        "editor": "this.textEditor",
        "renderer": "this.renderImage"
    },
    {
        "header": "Grid Column Header",
        "dataIndex": "tab_two_field_one",
        "width": 200,
        "sortable": true,
        "editor": "this.textEditor",
        "renderer": "this.renderImage"
    },
    {
        "header": "Grid Column Header",
        "dataIndex": "tab_two_field_two",
        "width": 200,
        "sortable": true,
        "editor": "this.textEditor",
        "renderer": "this.renderImage"
    }
]',
                'btntext' => 'Add New Item',
                'previewurl' => '',
                'jsonvarkey' => 'some-key',
                'autoResourceFolders' => true
            ],
            $migxTemplateVariableInput->getInputProperties(true),
            'Compare MIGX input properties'
        );

    }

    /**
     * @param \LCI\Blend\Helpers\MIGX\Field $field
     */
    protected function setFieldData(\LCI\Blend\Helpers\MIGX\Field $field)
    {
        $field
            ->setCaption('My Field')
            ->setDescription('Fill in...')
            ->setInputTemplateVariableType('listbox')
            ->setShowInGrid(true)
            ->setGridHeader('Grid Column Header')
            ->setGridWidth(200)
            ->setGridRenderer('this.renderImage')
            ->setGridEditor('this.textEditor');

        /** @var \LCI\Blend\Helpers\TVInput\OptionValues $optionValues */
        $optionValues = $field->loadOptionValues();
        $optionValues
            ->setOption('Option 1', 1)
            ->setOption('Option 2')
            ->setOption('Other', 'z');
    }

    /**
     * @throws \LCI\Blend\Exception\MigratorException
     */
    public function NOtestRemoveBlend()
    {
        if (BLEND_CLEAN_UP) {
            $this->blender->uninstall();

            $this->assertEquals(
                false,
                $this->blender->isBlendInstalledInModx()
            );
        }
    }
}
