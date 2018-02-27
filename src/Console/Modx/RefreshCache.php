<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 2:16 PM
 */

namespace LCI\Blend\Console\Modx;

use LCI\Blend\Console\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class RefreshCache
 * @package LCI\Blend\Console\Modx
 */
class RefreshCache extends BaseCommand
{
    public $loadMODX = true;

    protected function configure()
    {
        $this
            ->setName('modx:refresh-cache')
            ->setDescription('Refresh MODX Cache');
        // @TODO options for cache partitions: https://docs.modx.com/xpdo/2.x/advanced-features/caching

    }

    /**
     * Runs the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->modx->cacheManager->refresh();
        $output->writeln('### Cache has been refreshed(cleared) ###');
    }
}
