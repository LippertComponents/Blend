<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LCI\Blend\Blender;
use League\CLImate\CLImate;

require_once 'config.php';

class BaseBlend extends TestCase
{
    /** @var \MODx */
    protected $modx;

    /** @var Blender */
    protected $blender;

    /** @var CLImate */
    protected $climate;

    protected function loadDependentClasses()
    {
        if (!is_object($this->modx)) {
            $this->modx = new modX();

            $this->modx->initialize('mgr');
        }
        if (!is_object($this->climate)) {
            /** @var CLImate climate */
            $this->climate = new CLImate;
        }
        if (!is_object($this->blender)) {
            /** @var Blender */
            $this->blender = new Blender($this->modx, ['blend_modx_migration_dir' => BLEND_MODX_MIGRATION_PATH]);
            $this->blender->setClimate($this->climate);
        }
    }
}
