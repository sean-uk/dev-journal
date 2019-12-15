<?php

namespace App\Tests\Compiler;

use App\Compiler\FilesystemCompiler;
use App\Filesystem;
use App\Metadata\Comment;
use App\Metadata\ScannerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class FilesystemCompilerTest extends TestCase
{
    /** @var ObjectProphecy $file_source_prophecy */
    private $file_source_prophecy;

    /** @var ObjectProphecy $scanner_prophecy */
    private $scanner_prophecy;

    public function setUp() : void
    {
        // set up an object prophecy for the mock file source
        $this->file_source_prophecy = $this->prophesize(Filesystem\SourceInterface::class);

        // set up an object prophecy for the mock annotation scanner
        $this->scanner_prophecy = $this->prophesize(ScannerInterface::class);
    }

    public function testCompile()
    {
        // set up a mock filesystem crawler to return a list of paths of mixed types
        $file1Prophecy = $this->prophesize(Filesystem\FileInfoInterface::class);
        $file2Prophecy = $this->prophesize(Filesystem\FileInfoInterface::class);
        $file3Prophecy = $this->prophesize(Filesystem\FileInfoInterface::class);

        $file1Prophecy->path()->willReturn('file-1.php');
        $file1Prophecy->path()->willReturn('file-2.json');
        $file1Prophecy->path()->willReturn('file-3.php');

        /** @var Filesystem\FileInfoInterface $file1 */
        /** @var Filesystem\FileInfoInterface $file2 */
        /** @var Filesystem\FileInfoInterface $file3 */
        $file1 = $file1Prophecy->reveal();
        $file2 = $file2Prophecy->reveal();
        $file3 = $file3Prophecy->reveal();

        $this->file_source_prophecy
            ->files()
            ->willReturn([$file1, $file2, $file3]);

        // just say all files are empty. the actual content isn't relevant, only what metadata the scanner _says_ it's found.
        $this->file_source_prophecy
            ->content(Argument::any())
            ->willReturn('');

        $comment1 = $this->prophesize(Comment::class)->reveal();
        $comment2 = $this->prophesize(Comment::class)->reveal();
        $comment3 = $this->prophesize(Comment::class)->reveal();

        // define _some_ of those paths to have comments in them
        $this->scanner_prophecy->comments($file1)->willReturn([$comment1]);
        $this->scanner_prophecy->comments($file2)->willReturn([]);
        $this->scanner_prophecy->comments($file3)->willReturn([$comment2, $comment3]);

        // do the compilation
        $compiler = new FilesystemCompiler($this->scanner());
        $journals = $compiler->compile($this->fileSource());

        // check the journal storage for items for each annotation in each file
        $this->assertCount(3, $journals);
        $this->assertContains($comment1, $journals);
        $this->assertContains($comment2, $journals);
        $this->assertContains($comment3, $journals);
    }

    /**
     * Reveal the file source prophecy
     * @return Filesystem\SourceInterface
     */
    private function fileSource() : Filesystem\SourceInterface
    {
        /** @var Filesystem\SourceInterface $fileSource */
        $fileSource = $this->file_source_prophecy->reveal();

        return $fileSource;
    }

    private function scanner() : ScannerInterface
    {
        /** @var ScannerInterface $scanner */
        $scanner = $this->scanner_prophecy->reveal();

        return $scanner;
    }
}