<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 2:21 PM
 */

namespace LCI\Blend\Console;

use modX;
use LCI\Blend\Blender;
use LCI\MODX\Console\Command\BaseCommand as Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseCommand
 *
 * @package LCI\Blend\Console\BaseCommand
 */
abstract class BaseCommand extends Command
{
    /** @var \modX $modx */
    protected $modx;

    /** @var \LCI\Blend\Blender */
    protected $blender;

    protected $loadMODX = true;

    /**
     * Initializes the command just after the input has been validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        if ($this->loadMODX) {
            $this->modx = $this->console->loadMODX();
            $local_migration_path = getenv('BLEND_LOCAL_MIGRATION_PATH');
            if (!$local_migration_path) {
                $local_migration_path = MODX_CORE_PATH.'components/blend/';
            }

            $this->blender = new Blender(
                $this->modx,
                $this->consoleUserInteractionHandler,
                [
                    'blend_modx_migration_dir' => $local_migration_path,
                ]
            );

            $this->blender->setVerbose($output->getVerbosity());
        }
    }
}
