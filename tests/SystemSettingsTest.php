<?php
//declare(strict_types=1);

final class SystemSettingsTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    protected $test_system_settings = [
        0 => [
            'columns' => [
                'key' => 'site_name',
                'value' => 'Blend Site',
                'xtype' => 'textfield',
                'namespace' => 'core',
                'area' => 'site',
            ],
            'primaryKeyHistory' => [],
            'related' => [],
        ],
        1 => [
            'columns' => [
                'key' => 'blend_system_setting_test',
                'value' => 'This is only a test, I am safe to delete',
                'xtype' => 'textfield',
                'namespace' => 'core',
                'area' => 'site',
            ],
            'primaryKeyHistory' => [],
            'related' => [],
        ],
        2 => [
            'columns' => [
                'key' => 'site_status',
                'value' => '0',
                'xtype' => 'combo-boolean',
                'namespace' => 'core',
                'area' => 'site',
            ],
            'primaryKeyHistory' => [],
            'related' => [],
        ]
    ];

    public function testBlendManySystemSettings()
    {
        $this->assertEquals(
            true,
            $this->blender->blendManySystemSettings($this->test_system_settings, BLEND_TEST_SEEDS_DIR),
            'blendManySystemSettings attempted '
        );

        foreach ($this->test_system_settings as $count => $data) {
            $setting = $data['columns'];
            $systemSetting = $this->modx->getObject('modSystemSetting', $setting['key']);

            $this->assertInstanceOf(
                '\modSystemSetting',
                $systemSetting,
                'Validate system setting exists that was attempted to be blended '.$setting['key']
            );
            $actual = $systemSetting->toArray();
            unset($actual['editedon']);
            $this->assertEquals(
                $setting,
                $actual,
                $setting['key'].' blend compared'
            );
        }
    }

    /**
     * @depends testBlendManySystemSettings
     */
    public function testRevertBlendManySystemSettings()
    {
        // @TODO validate the reverted data
        $this->assertEquals(
            true,
            $this->blender->revertBlendManySystemSettings($this->test_system_settings, BLEND_TEST_SEEDS_DIR),
            'Revert system settings attempted '
        );
    }


    public function testMakeSystemSettingsSeeds()
    {
        $setting = [
            'key' => 'testSystemSetting2',
            'value' => 'Blend Site',
            'xtype' => 'textfield',
            'namespace' => 'core',
            'area' => 'site',
        ];

        // Make test setting:
        $testSystemSetting2 = $this->modx->getObject('modSystemSetting', $setting['key']);

        if (is_object($testSystemSetting2)) {
            $this->assertEquals(
                false,
                $testSystemSetting2,
                $setting['key'].' already exists'
            );

        } else {
            $testSystemSetting2 = $this->modx->newObject('modSystemSetting');
            $testSystemSetting2->fromArray($setting);
            $testSystemSetting2->set('key', $setting['key']);

            $this->assertEquals(
                true,
                $testSystemSetting2->save(),
                $setting['key'].' attempt to save system setting'
            );
        }

        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seed_data = $this->blender->getSeedMaker()->makeSystemSettingSeeds(['key' => $setting['key']]);

        $this->assertEquals(
            $this->removeStringLineEndings($this->removeDateFromStringArrayValue($this->getStringAfterFirstComment(file_get_contents(BLEND_COMPARE_DIRECTORY.$setting['key'].'.migration.php')))),
            $this->removeStringLineEndings($this->removeDateFromStringArrayValue($this->getStringAfterFirstComment(file_get_contents($this->blender->getMigrationPath().'m2018_01_10_093000_Systemsettings.php')))),
            'Comparing existing testSystemSetting2 migration file with generated file'
        );

        $this->blender->setSeedsDir($actual_timestamp);
    }

    /**
     * @depends testMakeSystemSettingsSeeds
     */
    public function testCleanUpMakeSystemSettingsSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $setting_name = 'testSystemSetting2';

        if (BLEND_CLEAN_UP) {
            // Remove created test snippet:
            $testSystemSetting2 = $this->modx->getObject('modSystemSetting', ['key' => $setting_name]);
            if (is_object($testSystemSetting2)) {
                $testSystemSetting2->remove();

            }

            $this->assertEquals(
                true,
                $this->blender->removeMigrationFile('', 'systemSettings'),
                'Remove created testSystemSetting2 migration seed file'
            );
        }
        $this->blender->setSeedsDir($actual_timestamp);
    }


    public function testSystemSettingMigration()
    {
        $migration = 'SystemSettingMigrationExample';
        $setting = [
            'key' => 'testSystemSettingMigration',
            'value' => 'Blend Site',
            'xtype' => 'textfield',
            'namespace' => 'core',
            'area' => 'site',
        ];

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $testSystemSetting3 = $this->modx->getObject('modSystemSetting', ['key' => $setting['key']]);
        $this->assertInstanceOf(
            '\modSystemSetting',
            $testSystemSetting3,
            'Validate testSystemSettingMigration that the system setting was created '.$setting['key']
        );

        if ($testSystemSetting3 instanceof \modSystemSetting) {
            $this->assertEquals(
                $setting['key'],
                $testSystemSetting3->get('key'),
                'Compare system setting key'
            );

            $this->assertEquals(
                $setting['value'],
                $testSystemSetting3->get('value'),
                'Compare system setting value'
            );

            $this->assertEquals(
                $setting['namespace'],
                $testSystemSetting3->get('namespace'),
                'Compare system setting namespace'
            );
        }
    }

    /**
     * @depends testSystemSettingMigration
     */
    public function testSystemSettingRevertMigration()
    {
        $migration = 'SystemSettingMigrationExample';
        $setting_key = 'testSystemSettingMigration';

        $testSystemSetting3 = $this->modx->getObject('modSystemSetting', ['key' => $setting_key]);

        $this->assertInstanceOf(
            '\modSystemSetting',
            $testSystemSetting3,
            'Validate SystemSettingMigrationExample did create a system setting '.$setting_key
        );

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $testSystemSetting4 = $this->modx->getObject('modSystemSetting', ['key' => $setting_key]);

        $this->assertEquals(
            false,
            $testSystemSetting4,
            'Compare SystemSettingMigrationExample revert, setting should be empty/false'
        );
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
}
