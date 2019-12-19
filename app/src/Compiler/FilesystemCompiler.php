<?php

namespace App\Compiler;

use App\JournalAnnotation;
use App\Entity\JournalEntry;
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

    /** @var JournalAnnotation\Parser */
    private $parser;

    /**
     * FilesystemCompiler constructor.
     *
     * @param ScannerInterface $scanner
     * @param JournalAnnotation\Parser $annotationParser
     */
    public function __construct(ScannerInterface $scanner, JournalAnnotation\Parser $annotationParser)
    {
        $this->scanner = $scanner;
        $this->parser = $annotationParser;
    }

    /**
     * Extract all comments from a given file source
     *
     * @param SourceInterface $source a file source
     * @param string|null $path the path under the file source to compile from. if null, all files will be used
     * @return JournalEntry[] comments extracted from the files
     */
    public function compile(SourceInterface $source, ?string $path = null) : array
    {
        // get the files (under the given path, if set)
        $files = $source->files($path);

        // for each one, use the scanner to get it's comments
        /** @var Comment[] $comments */
        $comments = [];
        foreach ($files as $file) {

            // get comments in the file
            $fileComments = $this->scanner->comments($file);
            $comments = array_merge($comments, $fileComments);
        }

        // now extract the journal entries in the comments
        $journals = [];
        foreach ($comments as $comment) {
            $commentJournals = $this->parser->parse($comment->content());
            $journals = array_merge($journals, $commentJournals);
        }

        return $journals;
    }
}