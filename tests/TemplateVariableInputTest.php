<?php

use LCI\Blend\Helpers\TemplateVariableInput;

final class TemplateVariableInputTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = false;

    /** @var array  */
    protected $expected_init_array = [
        'minLength' => '',
        'maxLength' => '',
        'regex' => '',
        'regexText' => '',
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_init_autotag = [
        'parent_resources' => '',
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_init_checkbox_option = [
        'columns' => 1,
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_init_date = [
        'disabledDates' => '',
        'disabledDays' => '',
        'minDateValue' => '',
        'minTimeValue' => '',
        'maxDateValue' => '',
        'maxTimeValue' => '',
        'startDay => ',
        'timeIncrement' => '',
        'hideTime' => false,
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_init_listbox = [
        'listWidth' => '',
        'title' => '',
        'listEmptyText' => '',
        'forceSelection' => false,
        'typeAhead' => true,
        'typeAheadDelay' => '',
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_init_listbox_multi = [
        'listWidth' => '',
        'title' => '',
        'listEmptyText' => '',
        'forceSelection' => false,
        'typeAhead' => true,
        'typeAheadDelay' => '',
        'stackItems' => false,
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_init_number = [
        'allowDecimals' => true,
        'allowNegative' => true,
        'decimalPrecision' => 2,
        'decimalSeparator' => '.',
        'maxvalue' => '',
        'minValue' => '',
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_init_resource_list = [
        'showNone' => false,
        'parents' => '',
        'depth' => 10,
        'includeParent' => false,
        'limitRelatedContext' => false,
        'where' => '',
        'limit' => 0,
        'allowBlank' => true
    ];

    /** @var array  */
    protected $expected_text_complete = [
        'minLength' => 10,
        'maxLength' => 100,
        'regex' => '[0-9]{3}\-[0-9]{3}\-[0-9]{4}',
        'regexText' => 'Failed to validate',
        'allowBlank' => false
    ];

    /** @var array  */
    protected $expected_checkbox_complete = [
        'columns' => 2,
        'allowBlank' => false
    ];

    /** @var array  */
    protected $expected_number_complete = [
        'allowDecimals' => false,
        'allowNegative' => false,
        'decimalPrecision' => 0,
        'decimalSeparator' => ',',
        'maxvalue' => 50,
        'minValue' => 2,
        'allowBlank' => false
    ];

    public function testInitTemplateVariableInputTextType()
    {
        // text
        $templateVariableInput = new TemplateVariableInput('text');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type text failed'
        );

        // email
        $templateVariableInput = new TemplateVariableInput('email');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type email failed'
        );

        // file
        $templateVariableInput = new TemplateVariableInput('file');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type file failed'
        );

        // hidden
        $templateVariableInput = new TemplateVariableInput('hidden');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type hidden failed'
        );

        // image
        $templateVariableInput = new TemplateVariableInput('image');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type image failed'
        );

        // richtext
        $templateVariableInput = new TemplateVariableInput('richtext');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type richtext failed'
        );

        // tag
        $templateVariableInput = new TemplateVariableInput('tag');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type tag failed'
        );

        // textarea
        $templateVariableInput = new TemplateVariableInput('textarea');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type textarea failed'
        );

        // url
        $templateVariableInput = new TemplateVariableInput('url');
        $this->assertEquals(
            $this->expected_init_array,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of type url failed'
        );

    }

    public function testInitTemplateVariableInputOtherTypes()
    {
        $templateVariableInput = new TemplateVariableInput('autotag');
        $this->assertEquals(
            $this->expected_init_autotag,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of autotag type failed'
        );

        // checkbox
        $templateVariableInput = new TemplateVariableInput('checkbox');
        $this->assertEquals(
            $this->expected_init_checkbox_option,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of checkbox type failed'
        );

        // option
        $templateVariableInput = new TemplateVariableInput('option');
        $this->assertEquals(
            $this->expected_init_checkbox_option,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of option[radio] type failed'
        );

        // date
        $templateVariableInput = new TemplateVariableInput('date');
        $this->assertEquals(
            $this->expected_init_date,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of date type failed'
        );

        // listbox
        $templateVariableInput = new TemplateVariableInput('listbox');
        $this->assertEquals(
            $this->expected_init_listbox,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of listbox[single] type failed'
        );

        // listbox-multiple
        $templateVariableInput = new TemplateVariableInput('listbox-multiple');
        $this->assertEquals(
            $this->expected_init_listbox_multi,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of listbox-multiple type failed'
        );

        // number
        $templateVariableInput = new TemplateVariableInput('number');
        $this->assertEquals(
            $this->expected_init_number,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of number type failed'
        );

        // resourcelist
        $templateVariableInput = new TemplateVariableInput('resourcelist');
        $this->assertEquals(
            $this->expected_init_resource_list,
            $templateVariableInput->getInputProperties(),
            'Init TemplateVariableInput of resourcelist type failed'
        );
    }

    /**
     * @depends testInitTemplateVariableInputTextType
     */
    public function testTemplateVariableInputTextType()
    {
        $templateVariableInput = new TemplateVariableInput('text');

        $templateVariableInput
            ->setAllowBlank(false)
            ->setTextMaxLength(100)
            ->setTextMinLength(10)
            ->setTextRegex('[0-9]{3}\-[0-9]{3}\-[0-9]{4}')
            ->setTextRegexText('Failed to validate');

        $this->assertEquals(
            $this->expected_text_complete,
            $templateVariableInput->getInputProperties(),
            'Complete TemplateVariableInput of text type failed'
        );
    }

    /**
     * @depends testInitTemplateVariableInputOtherTypes
     */
    public function testTemplateVariableInputCheckboxType()
    {
        $templateVariableInput = new TemplateVariableInput('checkbox');

        $templateVariableInput
            ->setAllowBlank(false)
            ->setColumns(2);

        $this->assertEquals(
            $this->expected_checkbox_complete,
            $templateVariableInput->getInputProperties(),
            'Complete TemplateVariableInput of checkbox type failed'
        );
    }

    /**
     * @depends testInitTemplateVariableInputOtherTypes
     */
    public function testTemplateVariableInputNumberType()
    {
        $templateVariableInput = new TemplateVariableInput('number');

        $templateVariableInput
            ->setAllowBlank(false)
            ->setNumberAllowDecimals(false)
            ->setNumberAllowNegative(false)
            ->setNumberDecimalPrecision(0)
            ->setNumberDecimalSeparator(',')
            ->setNumberMinValue(2)
            ->setNumberMaxvalue(50);

        $this->assertEquals(
            $this->expected_number_complete,
            $templateVariableInput->getInputProperties(),
            'Complete TemplateVariableInput of number type failed'
        );
    }
}