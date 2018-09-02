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

            $this->blender = new Blender(
                $this->modx,
                $this->consoleUserInteractionHandler,
                [
                    // @TODO rename:
                    'blend_modx_migration_dir' => BLEND_MY_MIGRATION_PATH,
                ]
            );
        }


    }

    // @TODO remove
    public function loadModxInstall()
    {
        /* to validate installation, instantiate the modX class and run a few tests */
        if (include_once (M_CORE_PATH . 'model/modx/modx.class.php')) {
            $modx = new modX(M_CORE_PATH . 'config/', [
                \xPDO::OPT_SETUP => true,
            ]);

            if (!is_object($modx) || !($modx instanceof modX)) {
                $errors[] = '<p>'.$this->lexicon('modx_err_instantiate').'</p>';
            } else {
                $modx->setLogTarget(array(
                    'target' => 'FILE',
                    'options' => array(
                        'filename' => 'install.' . MODX_CONFIG_KEY . '.' . strftime('%Y%m%dT%H%M%S') . '.log'
                    )
                ));

                /* try to initialize the mgr context */
                $modx->initialize('mgr');
                if (!$modx->isInitialized()) {
                    $errors[] = '<p>'.$this->lexicon('modx_err_instantiate_mgr').'</p>';
                }
            }
        } else {
            $errors[] = '<p>'.$this->lexicon('modx_class_err_nf').'</p>';
        }
    }
}
