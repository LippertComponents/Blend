<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 3:23 PM
 */

namespace LCI\Blend\Console;

use LCI\Blend\Migrations\MigrationsCreator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class GenerateMigration extends BaseCommand
{
    protected $loadMODX = false;

    /**
     * @see https://symfony.com/doc/current/console.html
     *
     */
    protected function configure()
    {
        $this
            ->setName('blend:generate')
            ->setDescription('Generate a Blend Migration Class')
            ->addOption(
                'name',
                'N',
                InputOption::VALUE_OPTIONAL,
                'The name of the generated migration file'
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Server type to run migrations as, default is master. Possible master, staging, dev and local',
                'master'
            )
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Optional set the directory path to write the migration. Note database/migrations will be added to the path and if needed created.',
                ''
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = (string)$input->getOption('name');
        $type = (string)$input->getOption('type');
        $path = (string)$input->getOption('path');

        $output->writeln('Generate name: '.$name);

        $migrationCreator = new MigrationsCreator($this->consoleUserInteractionHandler);

        if (empty($path)) {
            $config = $this->console->getConfig();
            if (isset($config['BLEND_LOCAL_MIGRATION_PATH'])) {
                $path = $config['BLEND_LOCAL_MIGRATION_PATH'];

            } else {
                // MODX components path
                if (defined('MODX_CORE_PATH')) {
                    $path = MODX_CORE_PATH;
                } else {
                    $path = dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR;
                }
                $path .= 'components/';

                if (file_exists($path)) {
                    $path .= 'blend/';
                    if (!file_exists($path)) {
                        mkdir($path);
                    }
                }
            }
        }

        $success = $migrationCreator
            ->setVerbose($output->getVerbosity())
            ->setName($name)
            ->setDescription('')
            ->setServerType($type)
            ->setBaseMigrationsPath($path)
            ->createBlankMigrationClassFile();

        if (!$success) {
            $output->writeln('Could not write file');
        }
    }
}