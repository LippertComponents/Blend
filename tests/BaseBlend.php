<?php
//declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LCI\Blend\Blender;
use LCI\Blend\Helpers\SimpleCache;
use LCI\MODX\Console\Helpers\VoidUserInteractionHandler;

class BaseBlend extends TestCase
{
    /** @var \modX  An xPDO instance for this TestCase. */
    protected $modx;

    /** @var Blender */
    protected $blender;

    /** @var \LCI\MODX\Console\Helpers\UserInteractionHandler */
    protected $consoleUserInteractionHandler;

    /** @var bool  */
    protected $install_blend = false;

    /**
     * @var modX A static modX fixture.
     */
    public static $fixture = null;

    /**
     * Setup static properties when loading the test cases.
     */
    public static function setUpBeforeClass()
    {
        // copy from database.. to temp/database/...
        $fileHelper = new SimpleCache(BLEND_TEST_TEMP_MIGRATION_PATH.'database/');
        $fileHelper
            ->setMode('0777')
            ->copyDirectory(BLEND_TEST_MIGRATION_PATH.'database/', BLEND_TEST_TEMP_MIGRATION_PATH.'database/');
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        if (BLEND_CLEAN_UP) {
            // delete files form temp:
            $fileHelper = new SimpleCache(BLEND_TEST_TEMP_MIGRATION_PATH);
            $fileHelper->deleteDirectory(BLEND_TEST_TEMP_MIGRATION_PATH.'database/');
        }
    }

    /**
     * Grab a persistent instance of the xPDO class to share sample model data
     * across multiple tests and test suites.
     *
     * @param bool $new Indicate if a new singleton should be created
     *
     * @return xPDO An xPDO object instance.
     */
    public static function getInstance($new = false)
    {
        if ($new || !is_object(self::$fixture)) {
            $modx = new modX();

            $modx->initialize('mgr');

            if (is_object($modx)) {
                self::$fixture = $modx;
            }
        }
        self::$fixture->cacheManager->refresh();
        return self::$fixture;
    }

    /**
     * Set up the xPDO(modx) fixture for each test case.
     */
    protected function setUp()
    {
        $this->modx = self::getInstance();


        $this->consoleUserInteractionHandler = new VoidUserInteractionHandler();

        $this->blender = new Blender($this->modx, $this->consoleUserInteractionHandler, ['blend_modx_migration_dir' => BLEND_TEST_TEMP_MIGRATION_PATH]);

        if ($this->install_blend) {
            $this->blender->install();
        }
    }

    /**
     * Tear down the xPDO(modx) fixture after each test case.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->modx = null;
    }

    /**
     * @param $string
     *
     * @return bool|string
     */
    protected function getStringAfterFirstComment($string)
    {
        return substr($string, (int)strpos($string, '*/') );
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    protected function removeStringLineEndings($string)
    {
        return str_replace(["\r",  "\n", "\r\n"], '', $string);
    }

    protected function removePHPtags($string)
    {
        return trim(ltrim($string, '<?php'));
    }

    /**
     * @param string $string
     * @return mixed
     */
    protected function removeDateFromStringArrayValue($string, $key='editedon')
    {
        $pattern = '/(\''.$key.'\' => \')(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})(\')/';
        $string = preg_replace($pattern, '\''.$key.'\' => null', $string);

        // make the null lower case: answer
        return str_replace(['\''.$key.'\' => NULL', '\''.$key.'\' => null'], '\''.$key.'\' => null', $string);
    }
}
