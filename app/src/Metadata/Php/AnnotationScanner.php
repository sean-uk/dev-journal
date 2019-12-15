<?php

namespace App\Metadata\Php;

use App\Filesystem\FileInfoInterface;
use App\Filesystem;
use App\Metadata\Comment;
use App\Metadata\ScannerInterface;
use PhpParser;

/**
 * Class PhpAnnotationScanner
 *
 * Extract journal entries from annotations in PHP file content
 *
 * @package App\Metadata
 */
class AnnotationScanner implements ScannerInterface
{
    /** @var Filesystem\SourceInterface $filesystem */
    private $filesystem;

    /** @var PhpParser\Parser $parser */
    private $parser;

    private $finder;

    /** @var PhpParser\PrettyPrinterAbstract $printer */
    private $printer;

    /**
     * PhpAnnotationScanner constructor.
     * @param Filesystem\SourceInterface $filesystem
     * @param PhpParser\Parser $phpParser
     * @param PhpParser\NodeFinder $nodeFinder
     * @param PhpParser\PrettyPrinterAbstract $prettyPrinter
     */
    public function __construct(
        Filesystem\SourceInterface $filesystem,
        PhpParser\Parser $phpParser,
        PhpParser\NodeFinder $nodeFinder,
        PhpParser\PrettyPrinterAbstract $prettyPrinter
    ) {
        $this->filesystem = $filesystem;
        $this->parser = $phpParser;
        $this->finder = $nodeFinder;
        $this->printer = $prettyPrinter;
    }

    /**
     * @inheritDoc
     */
    public function comments(FileInfoInterface $fileInfo): array
    {
        // get the contents of the file
        $contents = $this->filesystem->content($fileInfo->path());

        // parse the content as PHP source
        $stmts = $this->parser->parse($contents);

        // find all comments
        $commentStmts = $this->finder->findInstanceOf($stmts, PhpParser\Comment::class);

        // create comment entities with the contents of each node found
        $comments = [];
        foreach ($commentStmts as $commentStmt) {
            $commentText = $this->printer->prettyPrint([$commentStmt]);
            $comments[] = new Comment($commentText);
        }

        return $comments;
    }
}