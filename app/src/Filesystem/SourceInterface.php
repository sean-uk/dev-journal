<?php


namespace App\Filesystem;

/**
 * Interface FilesystemInterface
 *
 * A source of files to be processed for annotations
 *
 * @package App\Filesystem
 * @todo files could in theory be enormous. use streams/generators?
 */
interface SourceInterface
{
    /**
     * List file paths under the source
     *
     * @param string|null $filePath
     * @return FileInfoInterface[]
     */
    public function files(?string $path = null) : array;

    /**
     * @param string $path
     * @return string
     */
    public function content(string $path) : string;
}