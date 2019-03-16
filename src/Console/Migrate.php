<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 3:23 PM
 */

namespace LCI\Blend\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class Migrate extends BaseCommand
{
    /**
     * @see https://symfony.com/doc/current/console.html
     *
     */
    protected function configure()
    {
        $this
            ->setName('blend:migrate')
            ->setDescription('Run Blend Data migrations')
            ->addOption(
                'name',
                'N',
                InputOption::VALUE_OPTIONAL,
                'The name of the migration file to run. Or when used with the -g option, name the generated migration file'
            )
            ->addOption(
                'method',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Up or down(rollback), default is up',
                'up'
            )
            ->addOption(
                'id',
                'i',
                InputOption::VALUE_OPTIONAL,
                'ID of migration to run'
            )
            ->addOption(
                'count',
                'c',
                InputOption::VALUE_OPTIONAL,
                'How many to rollback when using the [--method down] option'
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Server type to run migrations as, default is master. Possible master, staging, dev and local',
                'master'
            )
            ->addOption(
                'package',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Enter a valid package name, like lci/stockpile',
                ''
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \LCI\Blend\Exception\MigratorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = (string)$input->getOption('name');
        $type = (string)$input->getOption('type');
        $id = $input->getOption('id');
        $count = $input->getOption('count');

        $method = $input->getOption('method');
        $package = $input->getOption('package');
        if (!empty($package)) {
            $this->blender->setProject($package);
        }

        $this->blender->runMigration($method, $type, $count, $id, $name);
    }
}