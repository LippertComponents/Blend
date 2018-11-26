<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 2/15/18
 * Time: 2:43 PM
 */

namespace LCI\Blend;

use LCI\MODX\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class Application extends ConsoleApplication
{
    protected static $logo = __DIR__ . '/art/blend.txt';

    protected static $name = 'Blend Console';

    protected static $version = '1.1.6';
}