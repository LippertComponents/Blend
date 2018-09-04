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

use Symfony\Component\Console\Input\InputArgument;

class Blend extends BaseCommand
{
    protected $exe_type = 'install';

    /**
     * @see https://symfony.com/doc/current/console.html
     * namespace:project/extra(-sub-part) verb (GET/POST/DELETE) --options
     */
    protected function configureWithConsole()
    {
        $activePackageCommands = new ActivePackageCommands($this->console);
        if ($activePackageCommands->isBlendInstalled()) {
            if ($activePackageCommands->isBlendRequireUpdate()) {
                $this->exe_type = 'update';
                $this
                    ->setName('blend:update')
                    ->setDescription('An update to Blend is required')
                    ->addArgument(
                        'update',
                        InputArgument::OPTIONAL,
                        'Set to argument value to Y to update.',
                        'Y'
                    );

            } else {
                $this->exe_type = 'uninstall';
                $this
                    ->setName('blend:uninstall')
                    ->setDescription('Uninstall Blend');
            }

        } else {
            $this->exe_type = 'install';
            $this
                ->setName('blend:install')
                ->setDescription('Please install Blend');
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(__METHOD__);
        switch ($this->exe_type) {
            case 'install':
                $this->blender->install('up', true);
                break;
            case 'uninstall':
                $this->blender->install('down', true);
                break;
            case 'update':
                if ($this->blender->requireUpdate()) {
                    $this->blender->update();
                }
                break;
            default:
                $output->writeln('Noting to do!');
        }

    }
}