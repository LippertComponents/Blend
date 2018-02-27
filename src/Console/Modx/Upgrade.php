<?php

namespace LCI\Blend\Console\Modx;

use LCI\Blend\Console\BaseCommand;
use LCI\Blend\Helpers\DownloadModxVersion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\Table;

/**
 * Class Upgrade
 * @package LCI\Blend\Console\Modx
 *
 * Class originally from the Gitify project: https://github.com/modmore/Gitify
 */
class Upgrade extends BaseCommand
{

    public $loadConfig = false;
    public $loadModx = true;

    protected function configure()
    {
        $this
            ->setName('modx:upgrade')
            ->setDescription('Downloads, configures and updates the current MODX installation.')
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'The version of MODX to upgrade, in the format 2.6.1-pl. Leave empty or specify "latest" to install the last stable release.',
                'latest'
            )
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_NONE,
                'List available versions '
            )
            ->addOption(
                'installed-version',
                'i',
                InputOption::VALUE_NONE,
                'Get the current MODX version installed.'
            )
            ->addOption(
                'branch',
                'b',
                InputOption::VALUE_OPTIONAL,
                'Install for a git branch rather then a release, ex 3.x '
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installed_version = $this->modx->getVersionData();

        if ($input->getOption('installed-version')) {
            $output->writeln('Installed MODX version is: '.$installed_version['full_appname']);
            return 1;

        } elseif ($input->getOption('list')) {
            DownloadModxVersion::showVersionsAsTable($output);
            return 1;

        }

        $downloadModxVersion = new DownloadModxVersion($output);
        $downloaded = false;

        if ($branch = $input->getOption('branch')) {
            $downloaded = $downloadModxVersion
                ->setDownloadDirectory(BLEND_CACHE_DIR)
                ->getGitBranch($branch);

        } else {
            $version = $input->getArgument('version');
            if ($version == 'latest') {
                foreach (DownloadModxVersion::RELEASE_ARCHIVE as $version => $date) {
                    break;
                }
            } elseif (!isset(DownloadModxVersion::RELEASE_ARCHIVE[$version])) {
                $output->writeln('<error>Error version: '.$version.' is can not be found in Blend</error>');

                return 0;
            }

            if (version_compare($version, $installed_version['full_version']) >= 0) {

                $output->writeln('<error>Error version: '.$version.' can not run update since it is less than or equal to the current installed version.</error>');

                return 0;
            }

            $downloaded = $downloadModxVersion
                ->setDownloadDirectory(BLEND_CACHE_DIR)
                ->getGitRelease($version);
        }


        if ($downloaded) {
            // finish, run migration:

            // update config?? why??

        }

        $output->writeln('Done! ' . $this->getRunStats());

        return 0;
    }

}
