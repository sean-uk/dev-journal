<?php


namespace App\Filesystem;

/**
 * Interface FileInfoInterface
 *
 * @package App\Filesystem
 */
interface FileInfoInterface
{
    /**
     * get the file's extension, if it has one
     *
     * @return string|null
     */
    public function extension();
}