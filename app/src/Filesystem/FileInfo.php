<?php

namespace App\Filesystem;

/**
 * Class FileInfo
 *
 * Basic file metadata
 *
 * @package App\Filesystem
 */
class FileInfo implements FileInfoInterface
{
    private $path;

    /**
     * FileInfo constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }
}