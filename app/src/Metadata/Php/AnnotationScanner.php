<?php

namespace App\Metadata\Php;

use App\Filesystem;
use App\Filesystem\FileInfoInterface;
use App\Metadata\Comment;
use App\Metadata\ScannerInterface;
use PhpParser;
use PhpParser\NodeTraverser;

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

    /** @var CommentVisitor $comment_visitor */
    private $comment_visitor;

    /**
     * PhpAnnotationScanner constructor.
     * @param Filesystem\SourceInterface $filesystem
     * @param PhpParser\Parser $phpParser
     * @param CommentVisitor $commentVisitor
     */
    public function __construct(
        Filesystem\SourceInterface $filesystem,
        PhpParser\Parser $phpParser,
        CommentVisitor $commentVisitor
    ) {
        $this->filesystem = $filesystem;
        $this->comment_visitor = $commentVisitor;
        $this->parser = $phpParser;
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
        // (need a custom visitor \PhpParser\NodeFinder::findInstanceOf won't work. Comments aren't Nodes!)
        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->comment_visitor);
        $traverser->traverse($stmts);
        $comments = $this->comment_visitor->comments();

        // create comment entities with the contents of each node found
        $commentsEntities = [];
        foreach ($comments as $comment) {
            $commentText = $comment->getText();
            $commentsEntities[] = new Comment($commentText);
        }

        return $commentsEntities;
    }
}