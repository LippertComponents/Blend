<?php

use LCI\Blend\Helpers\ElementProperty;

final class ElementPropertyTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = false;

    /** @var array  */
    protected $expected_init_array = [
        'name' => 'InitTest',
        'desc' => '',
        'type' => 'textfield',
        'options' => [],
        'value' => '',
        'lexicon' => null,
        'area' => ''
    ];

    /** @var array  */
    protected $expected_number_array = [
        'name' => 'startID',
        'desc' => 'The Resource ID to start the query',
        'type' => 'number',
        'options' => [],
        'value' => 10,
        'lexicon' => 'core',
        'area' => 'Resources'
    ];

    /** @var array  */
    protected $expected_list_array = [
        'name' => 'someList',
        'desc' => 'Choose one from the list',
        'type' => 'list',
        'options' => [
            [
                'text' => 'Option 1',
                'value' => 'opt1'
            ],
            [
                'text' => 'Option 2',
                'value' => 'opt2'
            ]
        ],
        'value' => 'opt2',
        'lexicon' => 'extra',
        'area' => 'Lists'
    ];

    public function testInitElementProperty()
    {
        $elementProperty = new ElementProperty('InitTest');

        $this->assertEquals(
            $this->expected_init_array,
            $elementProperty->toArray(),
            'Init ElementProperty failed'
        );
    }

    /**
     * @depends testInitElementProperty
     */
    public function testNumberElementProperty()
    {
        $elementProperty = new ElementProperty('startID');

        $elementProperty
            ->setDescription('The Resource ID to start the query')
            ->setValue(10)
            ->setType('number')
            ->setArea('Resources')
            ->setLexicon('core');

        $this->assertEquals(
            $this->expected_number_array,
            $elementProperty->toArray(),
            'Number ElementProperty failed'
        );
    }

    /**
     * @depends testInitElementProperty
     */
    public function testListElementProperty()
    {
        $elementProperty = new ElementProperty('someList');

        $elementProperty
            ->setDescription('Choose one from the list')
            ->setType('list')
            ->addOption('Option 1', 'opt1')
            ->addOption('Option 2', 'opt2')
            ->setValue('opt2')
            ->setArea('Lists')
            ->setLexicon('extra');

        $this->assertEquals(
            $this->expected_list_array,
            $elementProperty->toArray(),
            'someList ElementProperty failed'
        );
    }

    /**
     * @depends testInitElementProperty
     */
    public function testAddOptionsListElementProperty()
    {
        $elementProperty = new ElementProperty('someList');

        $elementProperty
            ->setDescription('Choose one from the list')
            ->setType('list')
            ->addOption('Option 1', 'opt1')
            ->addOptions([
                [
                    'text' => 'Option 2',
                    'value' => 'opt2'
                ]
            ])
            ->setValue('opt2')
            ->setArea('Lists')
            ->setLexicon('extra');

        $this->assertEquals(
            $this->expected_list_array,
            $elementProperty->toArray(),
            'AddOptions someList ElementProperty failed'
        );
    }

    /**
     * @depends testInitElementProperty
     */
    public function testSetOptionsListElementProperty()
    {
        $elementProperty = new ElementProperty('someList');

        $elementProperty
            ->setDescription('Choose one from the list')
            ->setType('list')
            ->addOption('Option will be overwritten', 'removed')
            ->setOptions([
                [
                    'text' => 'Option 1',
                    'value' => 'opt1'
                ],
                [
                    'text' => 'Option 2',
                    'value' => 'opt2'
                ]
            ])
            ->setValue('opt2')
            ->setArea('Lists')
            ->setLexicon('extra');

        $this->assertEquals(
            $this->expected_list_array,
            $elementProperty->toArray(),
            'SetOptions someList ElementProperty failed'
        );
    }

}