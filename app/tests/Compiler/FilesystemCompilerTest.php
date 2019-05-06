<?php

namespace App\Tests\Compiler;

use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use App\Filesystem;

class FilesystemCompilerTest extends TestCase
{
    /** @var ObjectProphecy $file_source_prophecy */
    private $file_source_prophecy;

    public function setUp()
    {
        // set up an object prophecy for the mock file source
        $this->file_source_prophecy = $this->prophesize(Filesystem\SourceInterface::class);
    }

    public function testCompile()
    {
        // set up a mock filesystem crawler to return a list of paths of mixed types
        $file1 = $this->prophesize(Filesystem\FileInfoInterface::class);
        $file2 = $this->prophesize(Filesystem\FileInfoInterface::class);
        $file3 = $this->prophesize(Filesystem\FileInfoInterface::class);
        $this->file_source_prophecy
            ->files()
            ->willReturn([$file1->reveal(), $file2->reveal(), $file3->reveal()]);

        $file1
            ->extension()
            ->willReturn('php');
        $file2
            ->extension()
            ->willReturn('json');
        $file2
            ->extension()
            ->willReturn('php');

        // define _some_ of those paths to have annotations in them


        // do the compilation

        // check the journal storage for items for each annotation in each file
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
}