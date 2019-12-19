<?php

namespace App\Tests\Compiler;

use App\Compiler\FilesystemCompiler;
use App\Entity\JournalEntry;
use App\Filesystem;
use App\JournalAnnotation;
use App\Metadata\Comment;
use App\Metadata\ScannerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class FilesystemCompilerTest extends TestCase
{
    /** @var ScannerInterface $scanner_prophecy */
    private $scanner_prophecy;

    /** @var JournalAnnotation\Parser */
    private $annotation_parser_prophecy;

    public function setUp() : void
    {
        $this->scanner_prophecy = $this->prophesize(ScannerInterface::class);
        $this->annotation_parser_prophecy = $this->prophesize(JournalAnnotation\Parser::class);
    }

    public function testCompile()
    {
        // set up a mock filesystem crawler to return a list of paths of mixed types
        $file1 = $this->prophesize(Filesystem\FileInfoInterface::class)->reveal();
        $file2 = $this->prophesize(Filesystem\FileInfoInterface::class)->reveal();

        // mock up a file source to be compiled from
        $fileSource = $this->prophesize(Filesystem\SourceInterface::class);
        $fileSource
            ->files()
            ->willReturn([$file1, $file2]);

        // define _some_ of those paths to have comments in them
        $comment1Prophecy = $this->prophesize(Comment::class);
        $comment2Prophecy = $this->prophesize(Comment::class);
        $comment3Prophecy = $this->prophesize(Comment::class);
        $comment1Prophecy
            ->content()
            ->willReturn('comment-one');
        $comment2Prophecy
            ->content()
            ->willReturn('comment-two');
        $comment3Prophecy
            ->content()
            ->willReturn('comment-three');
        $this->scanner_prophecy->comments($file1)->willReturn([$comment1Prophecy->reveal()]);
        $this->scanner_prophecy->comments($file2)->willReturn([$comment2Prophecy->reveal(), $comment3Prophecy->reveal()]);

        // the comments should be parsed for journal entry annotations
        // we'll say there are three journal entries in two comments
        $journal1 = $this->prophesize(JournalEntry::class)->reveal();
        $journal2 = $this->prophesize(JournalEntry::class)->reveal();
        $journal3 = $this->prophesize(JournalEntry::class)->reveal();
        $this->annotation_parser_prophecy
            ->parse('comment-one')
            ->WillReturn([$journal1, $journal3]);
        $this->annotation_parser_prophecy
            ->parse('comment-two')
            ->willReturn([]);
        $this->annotation_parser_prophecy
            ->parse('comment-three')
            ->willReturn([$journal2]);

        // do the compilation
        $compiler = new FilesystemCompiler($this->scanner_prophecy->reveal(), $this->annotation_parser_prophecy->reveal());
        $journals = $compiler->compile($fileSource->reveal());

        // check the journal storage for items for each annotation in each file
        $this->assertCount(3, $journals);
        $this->assertContains($journal1, $journals);
        $this->assertContains($journal2, $journals);
        $this->assertContains($journal3, $journals);
    }
}