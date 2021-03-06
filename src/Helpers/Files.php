<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 3/13/18
 * Time: 1:11 PM
 */

namespace LCI\Blend\Helpers;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

trait Files
{
    /** @var string  */
    protected $mode = '0700';

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function setMode(string $mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @param string $source ~ full path of source
     * @param string $destination ~ full path of destination
     * @param int $file_count
     */
    public function copyDirectory($source, $destination, $file_count = 4000)
    {
        $destination = rtrim($destination, '\/\\');
        if (!is_dir($destination)) {
            $this->makeDirectory($destination);
        }

        /** @var \RecursiveDirectoryIterator $directoryIterator */
        $directoryIterator = new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS);

        if (isset($this->output) && $this->output instanceof OutputInterface) {
            $this->output->writeln('Now copying extracted files to '.$destination.' File count: '.$file_count);
            $progress = new ProgressBar($this->output, $file_count);
            $progress->start();

            $progress->setRedrawFrequency(10);
        }

        /** @var RecursiveIteratorIterator $recursiveIteratorIterator */
        $recursiveIteratorIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

        /** @var \DirectoryIterator $item */
        foreach ($recursiveIteratorIterator as $item) {
            if ($item->isDir()) {
                if (is_dir($destination.DIRECTORY_SEPARATOR.$recursiveIteratorIterator->getSubPathName())) {
                    continue;
                }
                $this->makeDirectory($destination.DIRECTORY_SEPARATOR.$recursiveIteratorIterator->getSubPathName());

            } else {
                copy($item, $destination.DIRECTORY_SEPARATOR.$recursiveIteratorIterator->getSubPathName());
            }

            if (isset($progress) && $progress instanceof ProgressBar) {
                $progress->advance();
            }

        }

        if (isset($progress) && $progress instanceof ProgressBar) {
            // ensures that the progress bar is at 100%
            $progress->finish();
            $this->output->writeln('');
        }

    }

    /**
     * @param string $directory
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        $directory = rtrim($directory, '\/\\');
        if (!empty($directory) && file_exists($directory)) {

            /** @var \RecursiveDirectoryIterator $directoryIterator */
            $directoryIterator = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);

            /** @var RecursiveIteratorIterator $recursiveIteratorIterator */
            $recursiveIteratorIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);

            /** @var  $file */
            foreach ($recursiveIteratorIterator as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }

            return rmdir($directory);
        }

        return false;
    }

    /**
     * @param $directory
     * @return bool
     */
    protected function makeDirectory($directory)
    {
        $created = file_exists($directory);
        if (!$created && $created = mkdir($directory)) {
            chmod($directory, $this->mode);
        }

        return $created;
    }

}