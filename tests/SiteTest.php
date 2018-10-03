<?php
//declare(strict_types=1);

final class SiteTest extends BaseBlend
{
    /** @var bool  */
    protected $install_blend = true;

    public function testSiteRevertMigrationPrevious()
    {
        // Clean up any previous tests if there was a fatal error
        $migration = 'SiteExample';
        $snippet_name = 'snippetSiteExample';

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $snippetSiteExample = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);

        $this->assertEquals(
            false,
            $snippetSiteExample,
            'Compare snippetSiteExample, should be empty/false'
        );
    }

    // 1 run migration to load complete example data:
    public function testSiteMigration()
    {
        $migration = 'SiteExample';
        $snippet_name = 'snippetSiteExample';

        $this->blender->runMigration('up', 'master', 0, 0, $migration);

        $snippetSiteExample = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);
        $this->assertInstanceOf(
            '\modSnippet',
            $snippetSiteExample,
            'Validate testSnippetMigration that snippet was created '.$snippet_name
        );
    }

    /**
     * @depends testSiteMigration
     */
    public function testMakeSiteSeeds()
    {
        $actual_timestamp = $this->blender->getSeedsDir();
        $this->blender->setSeedsDir(BLEND_TEST_SEEDS_DIR);

        $seeds_directory = $this->blender->getMigrationName('site', 'SiteExample');

        $this->blender->getSeedMaker()->makeSiteSeed('master', 'SiteExample');

        // does migration file exist?
        $this->assertEquals(
            true,
            file_exists($this->blender->getMigrationPath().'m2018_01_10_093000_SiteExample.php'),
            'Checking if site example migration seed was created'
        );

        // compare contents of seed files:
        $site_data = require_once BLEND_COMPARE_DIRECTORY.'SiteExample/getSiteData.php';

        $compare_data = $site_data['compare'];
        $generated_data = $site_data['generated'];

        $seed_path = $this->blender->getSeedsPath($seeds_directory);

        foreach ($compare_data as $seed_type => $seed_keys) {
            if ($seed_type == 'systemSettings') {
                // @TODO
                continue;
            }

            foreach ($seed_keys as $count => $seed_key) {

                $comment = 'Comparing seed contents for seed_type ' . $seed_type . ' with key: ';
                if (!is_array($seed_key)) {
                    $this->assertEquals(
                        true,
                        in_array($seed_key, $generated_data[$seed_type]),
                        'Comparing existing seed_type ' . $seed_type . ' for key: ' . $seed_key
                    );
                    $comment .= $seed_key;
                }

                $file = '';
                switch ($seed_type) {
                    case 'contexts':
                        $file = 'contexts' . DIRECTORY_SEPARATOR .$seed_key.'.cache.php';
                        break;
                    case 'mediaSources':
                        $file = 'media-sources'. DIRECTORY_SEPARATOR .$seed_key.'.cache.php';
                        break;
                    case 'resources':
                        foreach ($seed_key as $c => $key) {
                            $file = 'resources' . DIRECTORY_SEPARATOR . $count . DIRECTORY_SEPARATOR . $key . '.cache.php';

                            $this->compareSeedArray(
                                BLEND_COMPARE_DIRECTORY.'SiteExample' . DIRECTORY_SEPARATOR . $file,
                                $seed_path.$file,
                                $comment.$key.' in context: '.$count
                            );
                        }
                        continue;
                        break;

                    //Elements
                    case 'chunks':
                        $file = 'elements' . DIRECTORY_SEPARATOR . 'chunks'. DIRECTORY_SEPARATOR .$seed_key.'.cache.php';
                        break;
                    case 'plugins':
                        $file = 'elements' . DIRECTORY_SEPARATOR . 'plugins'. DIRECTORY_SEPARATOR .$seed_key.'.cache.php';
                        break;
                    case 'snippets':
                        $file = 'elements' . DIRECTORY_SEPARATOR . 'snippets'. DIRECTORY_SEPARATOR .$seed_key.'.cache.php';
                        break;
                    case 'templates':
                        $file = 'elements' . DIRECTORY_SEPARATOR . 'templates'. DIRECTORY_SEPARATOR .$seed_key.'.cache.php';
                        break;
                }

                // compare file contents:
                $this->compareSeedArray(BLEND_COMPARE_DIRECTORY.'SiteExample' . DIRECTORY_SEPARATOR . $file, $seed_path.$file, $comment);
            }
        }

        $this->blender->setSeedsDir($actual_timestamp);
    }

    protected function compareSeedArray($compare_path, $seed_path, $comment)
    {

        $fixed_data = require_once $compare_path;
        $generated_data = false;
        if (file_exists($seed_path)) {
            $generated_data = require_once $seed_path;
        }
        if (is_array($fixed_data)) {
            unset($fixed_data['columns']['createdon'], $fixed_data['columns']['editedon'], $fixed_data['columns']['id'],
                $fixed_data['columns']['createdby'],$fixed_data['columns']['properties']['visibility']);
        }
        if (is_array($generated_data)  ) {
            unset($generated_data['columns']['createdon'], $generated_data['columns']['editedon'], $generated_data['columns']['id'],
                $generated_data['columns']['createdby'], $generated_data['columns']['properties']['visibility']);
        }

        $this->assertEquals(
            $fixed_data,
            $generated_data,
            $comment
        );
    }


    /**
     *  depends testSiteMigration
     */
    public function testSiteRevertMigration()
    {
        $migration = 'SiteExample';
        $snippet_name = 'snippetSiteExample';

        $this->blender->runMigration('down', 'master', 0, 0, $migration);

        $snippetSiteExample = $this->modx->getObject('modSnippet', ['name' => $snippet_name]);

        $this->assertEquals(
            false,
            $snippetSiteExample,
            'Compare snippetSiteExample, should be empty/false'
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
