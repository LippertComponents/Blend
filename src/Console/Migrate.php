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
                'generate',
                'g',
                InputOption::VALUE_NONE,
                'Generate/create an empty migration class. Then you can build out a custom migration.'
            )
            ->addOption(
                'name',
                'n',
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
                'How many to rollback when using the [--method down] option, default is 1'
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Server type to run migrations as, default is master. Possible master, staging, dev and local',
                'master'
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
        $id = $input->getOption('id');
        $count = $input->getOption('count');

        if ( $input->getOption('generate') ) {
            $output->writeln('Generate name: '.$name);
            // create a blank migration class
            $this->blender->createBlankMigrationClassFile($name, $type);

        } else {
            $method = $input->getOption('method');
            $this->blender->runMigration($method, $type, $count, $id, $name);

        }
    }
}