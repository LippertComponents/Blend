<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/22/18
 * Time: 1:16 PM
 */

namespace LCI\Blend\Helpers;

use GuzzleHttp\Client;
use FilesystemIterator;
use League\Flysystem\Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class DownloadModxVersion
{
    const RELEASE_ARCHIVE = [
        'v2.6.1-pl' => '2017-12-14',
        'v2.6.0-pl' => '2017-11-01',
        'v2.5.8-pl' => '2017-09-13',
        'v2.5.7-pl' => '2017-04-20',
        'v2.5.6-pl' => '2017-03-28',
        'v2.5.5-pl' => '2017-02-08',
        'v2.5.4-pl' => '2017-01-03',
        'v2.5.3-pl' => '2017-01-03',
        'v2.5.2-pl' => '2016-11-14',
        'v2.5.1-pl' => '2016-07-21',
        'v2.5.0-pl' => '2016-04-21',
    ];

    /** @var string  */
    protected $git_project_version = '3.x';

    /** @var \GuzzleHttp\Client */
    protected $guzzleClient;

    /** @var string  */
    protected $download_directory = '';

    /** @var string  */
    protected $zip_file = '';

    /** @var null|OutputInterface  */
    protected $output = null;

    /**
     * DownloadModxVersion constructor.
     * @param null|OutputInterface $output
     */
    public function __construct($output=null)
    {
        $this->output = $output;
    }


    /**
     * @return string
     */
    public function getDownloadDirectory()
    {
        return $this->download_directory;
    }

    /**
     * @param string $download_directory
     * @return DownloadModxVersion
     */
    public function setDownloadDirectory($download_directory)
    {
        $this->download_directory = rtrim($download_directory, '/').DIRECTORY_SEPARATOR;

        if (!file_exists($this->download_directory)) {
            mkdir($this->download_directory, 0700, true);
        }
        return $this;
    }

    /**
     * @param OutputInterface $output
     */
    public static function showVersionsAsTable(OutputInterface $output)
    {
        $table_rows = [];
        foreach (DownloadModxVersion::RELEASE_ARCHIVE as $version => $date) {
            $table_rows[] = [$version, $date];
        };

        $table = new Table($output);
        $table
            ->setHeaders(array('Released Version', 'Date'))
            ->setRows($table_rows)
        ;
        $table->setStyle('compact');
        $table->render();
    }

    /**
     * @param string $branch
     * @return bool
     */
    public function getGitBranch($branch='3.x')
    {
        $this->git_project_version = $branch;
        $this->loadGuzzle();
        // download a branch:
        // 3.x composer update
        // https://github.com/modxcms/revolution/archive/3.x.zip

        $this->zip_file = $this->download_directory.'branch-'.$branch.'.zip';
        $this->getFromGit($branch);

        return $this->moveGitFiles(true, false, false);
    }

    /**
     * @param string $release
     * @return bool
     */
    public function getGitRelease($release='v2.6.1-pl')
    {
        $this->git_project_version = ltrim($release, 'v');
        $this->loadGuzzle();
        // download a release:
        // 2.x download from Git: https://github.com/modxcms/revolution/archive/v2.6.0-pl.zip
        $this->zip_file = $this->download_directory.$release.'.zip';
        $this->getFromGit($release);

        return $this->moveGitFiles(false);
    }

    protected function getFromGit($remote)
    {
        if ($this->output instanceof OutputInterface) {
            $this->output->writeln('Getting '.$remote.' and saving to '.$this->zip_file);
        }

        // @see http://docs.guzzlephp.org/en/stable/request-options.html
        $options = [
            'sink' => $this->zip_file,
        ];
        $this->guzzleClient->request('GET', $remote.'.zip', $options);
        // modx stats: https://help.github.com/articles/getting-the-download-count-for-your-releases/

        // @TODO errors
    }


    protected function loadGuzzle()
    {
        $this->guzzleClient = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://github.com/modxcms/revolution/archive/',
            // You can set any number of default request options.
            'timeout'  => 300.0,// for slow internet
        ]);
    }

    /**
     * @param bool $run_composer_update
     * @param bool $remove_setup
     * @param bool $remove_build
     * @return bool
     */
    protected function moveGitFiles($run_composer_update=true, $remove_setup=true, $remove_build=true)
    {
        // unzip into proper directory:
        $zip = new ZipArchive;
        if ($zip->open($this->zip_file) === true) {
            $file_count = $zip->numFiles;

            if ($this->output instanceof OutputInterface) {
                $this->output->writeln('Extracting '.$this->zip_file. ' to '.BLEND_CACHE_DIR.' File count: '.$file_count);
                $progress = new ProgressBar($this->output, $file_count);
                $progress->start();

                $progress->setRedrawFrequency(10);
            }

            for($i = 0; $i < $zip->numFiles; $i++) {
                $zip->extractTo(BLEND_CACHE_DIR, [$zip->getNameIndex($i)]);

                if (isset($progress) && $progress instanceof ProgressBar) {
                    $progress->advance();
                }
            }

            if (isset($progress) && $progress instanceof ProgressBar) {
                // ensures that the progress bar is at 100%
                $progress->finish();
                $this->output->writeln('');
            }
            $zip->close();

            // copy directory to project root:
            $this->copyExtractedDirectory($file_count);

            if ($remove_build) {
                // delete the _build:
                $this->deleteDirectory('_build');
            }
            if ($remove_setup) {
                // delete the setup:
                $this->deleteDirectory('setup');
            }

            // run composer update:
            if ($run_composer_update) {
                $this->updateComposer();
            }

            return true;

        } else {
            // error
            $this->output->writeln('<error>Error Extracting '.$this->zip_file. ' to '.BLEND_CACHE_DIR.'</error>');
        }
        return false;
    }

    protected function copyExtractedDirectory($file_count=4000)
    {
        $source = BLEND_CACHE_DIR.'revolution-'.$this->git_project_version;
        $destination = MODX_PATH;

        if (!is_dir($destination)) {
            mkdir($destination, 0700);
        }

        /** @var \RecursiveDirectoryIterator $directoryIterator */
        $directoryIterator = new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS);

        if ($this->output instanceof OutputInterface) {
            $this->output->writeln('Now copying extracted files to '.$destination.' File count: '.$file_count);
            $progress = new ProgressBar($this->output, $file_count);
            $progress->start();

            $progress->setRedrawFrequency(10);
        }


        /** @var RecursiveIteratorIterator $recursiveIteratorIterator */
        $recursiveIteratorIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

        /** @var \DirectoryIterator $item */
        foreach ($recursiveIteratorIterator as $item) {
            if ($item->isDir()) {
                if (is_dir($destination. DIRECTORY_SEPARATOR. $recursiveIteratorIterator->getSubPathName())) {
                    continue;
                }
                mkdir($destination . DIRECTORY_SEPARATOR . $recursiveIteratorIterator->getSubPathName());

            } else {
                copy($item, $destination . DIRECTORY_SEPARATOR . $recursiveIteratorIterator->getSubPathName());
            }

            if (isset($progress) && $progress instanceof ProgressBar) {
                $progress->advance();
            }

        }

        if (isset($progress) && $progress instanceof ProgressBar) {
            // ensures that the progress bar is at 100%
            $progress->finish();
            $this->output->writeln('');
        }

    }

    /**
     * @param string $directory
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        if (!empty($directory) && file_exists(MODX_PATH.$directory)) {
            $dir = MODX_PATH . $directory;

            /** @var \RecursiveDirectoryIterator $directoryIterator */
            $directoryIterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);

            /** @var RecursiveIteratorIterator $recursiveIteratorIterator */
            $recursiveIteratorIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);

            /** @var  $file */
            foreach ($recursiveIteratorIterator as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }

            return true;
        }

        return false;
    }

    public function updateComposer()
    {
        // Composer\Factory::getHomeDir() method
        // needs COMPOSER_HOME environment variable set
        putenv('COMPOSER_HOME=' . MODX_PATH . 'vendor/bin/composer');
        putenv('COMPOSER='.MODX_PATH.'composer.json');

        $output = new ConsoleOutput();
        $output->writeln('Run: composer install: COMPOSER='.MODX_PATH.'composer.json'.PHP_EOL.'COMPOSER_HOME=' . MODX_PATH . 'vendor/bin/composer');

        try {
            //ini_set('memory_limit', '1024M');
            // call `composer install` command programmatically
            $input = new ArrayInput([
                'command' => 'update',
                //'command' => 'install',
                //'--no-dev' => true,
                '--working-dir' => MODX_PATH,
                '--optimize-autoloader' => true,
                '--no-suggest' => true,
                '--no-interaction' => true,
                '--no-progress' => true,
                '--verbose' => true
            ]);
            //$input->setInteractive(false);
            $application = new Application();
            $application->setAutoExit(false); // prevent `$application->run` method from exitting the script

            // call `composer install` command programmatically
            $application->doRun($input, $output);

        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
        }
    }

}