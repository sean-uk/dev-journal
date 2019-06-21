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
     * get the file's path
     *
     * @return string
     */
    public function path() : string;
}