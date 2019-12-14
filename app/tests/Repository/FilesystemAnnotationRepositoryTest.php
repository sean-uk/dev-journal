<?php

namespace App\Tests\Repository;

use App\Compiler\FilesystemCompiler;
use App\Filesystem;
use App\Metadata;
use App\Repository\FilesystemAnnotationRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class FilesystemAnnotationRepositoryTest extends TestCase
{
    /** @var FilesystemCompiler $compiler_prophecy */
    private $compiler_prophecy;

    /** @var Filesystem\SourceInterface */
    private $file_source_prophecy;

    public function setUp(): void
    {
        parent::setUp();

        // create dependencies prophecies
        $this->compiler_prophecy = $this->prophesize(FilesystemCompiler::class);
        $this->file_source_prophecy = $this->prophesize(Filesystem\SourceInterface::class);
    }

    public function test_find() : void
    {
        // the annotations will need to be compiled from the file source
        $journalMetadata1 = $this->prophesize(Metadata\JournalMetadata::class);
        $journalMetadata2 = $this->prophesize(Metadata\JournalMetadata::class);
        $this->compiler_prophecy
            ->compile(Argument::any())
            ->willReturn([
                $journalMetadata1->reveal(),
                $journalMetadata2->reveal(),
            ]);

        // create a new repository
        $repo = new FilesystemAnnotationRepository($this->file_source_prophecy->reveal(), $this->compiler_prophecy->reveal());

        // check the repository
        $entries = $repo->find();

        // check that the right number and content of entries is found
        $this->assertCount(2, $entries);
        $this->assertContains($journalMetadata1->reveal(), $entries);
        $this->assertContains($journalMetadata2->reveal(), $entries);
    }
}