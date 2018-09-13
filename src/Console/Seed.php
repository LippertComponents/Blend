<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 3:24 PM
 */

namespace LCI\Blend\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class Seed extends BaseCommand
{

    /**
     * @see https://symfony.com/doc/current/console.html
     *
     */
    protected function configure()
    {
        $this
            ->setName('blend:seed')
            ->setDescription('Export Blend seeds, data migrations')
            ->addOption(
                'name',
                'n',
                InputOption::VALUE_OPTIONAL,
                'Append this value to current timestamp for the generated migration file and seeds directory'
            )
            ->addOption(
                'object',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Seed object, default is r, can be r(resource), c(chunk), p(plugin), s(snippet), x(systemSettings), t(template) or a(site)',
                'r'
            )
            ->addOption(
                'id',
                'i',
                InputOption::VALUE_OPTIONAL,
                'ID of migration to run'
            )
            ->addOption(
                'date',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Date since created or modified, ex: 2018-02-16'
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
        // what seeds script to run?
        $name = (string)$input->getOption('name');
        $type = $input->getOption('type');
        $object = $input->getOption('object');
        $id = $input->getOption('id');
        $date = $input->getOption('date');

        if ( $object == 'c' || $object == 'chunk' ) {
            $this->seedChunks($type, $name, $id);

        } elseif ( $object == 'p' || $object == 'plugin' ) {
            $this->seedPlugins($type, $name, $id);

        } elseif ( $object == 'r' || $object == 'resource' ) {
            $this->seedResources($type, $name, $id, $date);

        }  elseif ( $object == 's' || $object == 'snippet' ) {
            $this->seedSnippets($type, $name, $id);

        } elseif ( $object == 'x' || $object == 'systemSettings'  ) {
            $this->seedSystemSettings($type, $name, $id, $date);

        } elseif ( $object == 't' || $object == 'template'  ) {
            $this->seedTemplates($type, $name, $id);

        } elseif ( $object == 'a' || $object == 'site'  ) {
            $this->blender->getSeedMaker()->makeSiteSeed($type, $name);

        }
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedChunks($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modChunk');

        if (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);
            $criteria->orCondition(array(
                'name' => $id
            ));

        } else {
            $ids = explode(',', $this->consoleUserInteractionHandler->promptInput('Enter in a comma separated list of chunk names or IDs: ', ''));

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'name:IN' => $ids
            ));
        }

        $this->blender->getSeedMaker()->makeChunkSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedPlugins($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modPlugin');

        if (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);
            $criteria->orCondition(array(
                'name' => $id
            ));

        } else {
            $input = $this->consoleUserInteractionHandler->promptInput('Enter in a comma separated list of plugin names or IDs: ', '');
            $ids = explode(',', $input);

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'name:IN' => $ids
            ));
        }

        $this->blender->getSeedMaker()->makePluginSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     * @param string $date
     */
    protected function seedResources($type, $name, $id, $date)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modResource');

        if (isset($date) && !empty($date)) {
            $date = strtotime($date);
            $criteria->where([
                'editedon:>=' => $date
            ]);
            $criteria->orCondition(array(
                'createdon:>=' => $date
            ));

        } elseif (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);

        } else {
            $input = $this->consoleUserInteractionHandler->promptInput('Enter in a comma separated list of resource IDs: ', '2');
            $ids = explode(',', $input);

            $criteria->where([
                'id:IN' => $ids
            ]);

            if ($this->consoleUserInteractionHandler->promptConfirm('Would you like to include the parents?')) {
                // get parents:
                $query = $this->modx->newQuery('modResource', ['id:IN' => $ids]);
                $query->select(['modResource.parent']);
                $query->prepare();
                $criteria->orCondition('`modResource`.`id` IN(' . $query->toSQL() . ')');
            }

            if ($this->consoleUserInteractionHandler->promptConfirm('Would you like to include direct children?')) {
                // get direct children:
                $query = $this->modx->newQuery('modResource', ['parent:IN' => $ids]);
                $query->select(['modResource.id']);
                $query->prepare();
                $children_sql = $query->toSQL();
                $criteria->orCondition('`modResource`.`id` IN(' . $children_sql . ')');

                if ($this->consoleUserInteractionHandler->promptConfirm('Would you like to include direct grand children?')) {
                    // get grand children
                    $query = $this->modx->newQuery('modResource');
                    $query->select(['modResource.parent']);
                    $query->where('`modResource`.`id` IN('.$children_sql.')');
                    $query->prepare();
                    $criteria->orCondition('`modResource`.`id` IN('.$query->toSQL().')');
                }
            }
        }

        $this->blender->getSeedMaker()->makeResourceSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedSnippets($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modSnippet');

        if (!empty($id) && is_numeric($id)) {
            $criteria->where([
                'id' => $id
            ]);
            $criteria->orCondition(array(
                'name' => $id
            ));

        } else {
            $input = $this->consoleUserInteractionHandler->promptInput('Enter in a comma separated list of snippet names or IDs: ');
            $ids = explode(',', $input);

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'name:IN' => $ids
            ));
        }

        $this->blender->getSeedMaker()->makeSnippetSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $key
     * @param string $date
     */
    protected function seedSystemSettings($type, $name, $key, $date)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modSystemSetting');

        if (isset($date) && !empty($date)) {
            $criteria->where([
                'editedon:>=' => $date
            ]);

        } elseif (!empty($key) && strlen($key) > 1) {
            $criteria->where([
                'key' => $key
            ]);

        } else {
            $names = $this->consoleUserInteractionHandler->promptInput('Enter in a comma separated list of system settings: ');

            $criteria->where([
                'key:IN' => explode(',', $names)
            ]);
        }
        $this->blender->getSeedMaker()->makeSystemSettingSeeds($criteria, $type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $id
     */
    protected function seedTemplates($type, $name, $id)
    {
        /** @var \xPDOQuery $criteria */
        $criteria = $this->modx->newQuery('modTemplate');

        if (is_numeric($id) && $id > 0) {
            $criteria->where([
                'id' => $id
            ]);

        } else {
            $input = $this->consoleUserInteractionHandler->promptInput('Enter in a comma separated list of template names or IDs: ');

            $ids = explode(',', $input);

            $criteria->where([
                'id:IN' => $ids
            ]);
            $criteria->orCondition(array(
                'templatename:IN' => $ids
            ));

        }

        $this->blender->getSeedMaker()->makeTemplateSeeds($criteria, $type, $name);
    }

}