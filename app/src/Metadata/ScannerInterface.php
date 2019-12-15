<?php


namespace App\Metadata;

use App\Filesystem\FileInfoInterface;

/**
 * Interface ScannerInterface
 *
 * Something that can process and extract comments from a file's content
 *
 * @package App\Scanner
 */
interface ScannerInterface
{
    /**
     * @param FileInfoInterface $file
     * @return Comment[]
     */
    public function comments(FileInfoInterface $fileInfo) : array;
}