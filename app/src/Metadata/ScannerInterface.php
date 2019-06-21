<?php


namespace App\Metadata;

use App\Filesystem\FileInfoInterface;

/**
 * Interface ScannerInterface
 *
 * Something that can process and extract journal entries from a file
 *
 * @package App\Scanner
 */
interface ScannerInterface
{
    /**
     * @param FileInfoInterface $file
     * @return JournalMetadata[]
     */
    public function journalEntries(FileInfoInterface $fileInfo) : array;
}