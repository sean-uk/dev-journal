<?php

namespace App\Compiler;

use App\Filesystem\SourceInterface;
use App\Metadata\Comment;
use App\Metadata\ScannerInterface;

/**
 * Class FilesystemCompiler
 *
 * This class scans all the files in a filesystem under a given path and copies/extracts any comments it finds
 *
 * @package App\Compiler
 */
class FilesystemCompiler
{
    /** @var ScannerInterface $scanner */
    private $scanner;

    /**
     * FilesystemCompiler constructor.
     *
     * @param ScannerInterface $scanner
     */
    public function __construct(ScannerInterface $scanner)
    {
        $this->scanner = $scanner;
    }

    /**
     * Extract all comments from a given file source
     *
     * @param SourceInterface $source a file source
     * @return Comment[] comments extracted from the files
     */
    public function compile(SourceInterface $source) : array
    {
        // get the files
        $files = $source->files();

        // for each one, use the scanner to get it's journal metadata
        $journals = [];
        foreach ($files as $file) {

            // get journal metadata in content
            $fileJournals = $this->scanner->comments($file);
            foreach ($fileJournals as $fileJournal) {
                $journals[] = $fileJournal;
            }
        }

        return $journals;
    }
}