<?php


namespace App\Filesystem;

/**
 * Interface FilesystemInterface
 *
 * A source of files to be processed for annotations
 *
 * @package App\Filesystem
 */
interface SourceInterface
{
    /**
     * List all file paths under the source
     *
     * @return FileInfoInterface[]
     */
    public function files() : array;

    /**
     * @param string $path
     * @return string
     * @throws \RuntimeException
     */
    public function content(string $path) : string;
}