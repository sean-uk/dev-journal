<?php

namespace App\Tests\Command;

use App\Command\CompileAnnotationsCommand;
use App\Compiler\FilesystemCompiler;
use App\Entity\JournalEntry;
use App\Filesystem\FileInfoInterface;
use App\Filesystem\SourceInterface;
use App\Repository\AnnotationRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class CompileAnnotationsCommandTest extends TestCase
{
    /** @var FilesystemCompiler $compiler_prophecy */
    private $compiler_prophecy;

    /** @var AnnotationRepositoryInterface $annotation_repository_prophecy */
    private $annotation_repository_prophecy;

    /** @var SourceInterface $file_source_prophecy */
    private $file_source_prophecy;

    public function setUp(): void
    {
        parent::setUp();

        $this->compiler_prophecy = $this->prophesize(FilesystemCompiler::class);
        $this->annotation_repository_prophecy = $this->prophesize(AnnotationRepositoryInterface::class);
        $this->file_source_prophecy = $this->prophesize(SourceInterface::class);
    }

    public function test_execute() : void
    {
        // make the file source return some files for a particular some arbitrary location
        /** @var FileInfoInterface $file1 */
        $file1 = $this->prophesize(FileInfoInterface::class);
        $file1
            ->path()
            ->willReturn('/path/to/file/1');
        $this->file_source_prophecy
            ->files('/path/to/files')
            ->willReturn([
                $file1->reveal()
            ]);

        // stub the filesystem compiler to return a set of journal entries from the file source
        /** @var JournalEntry $entry1 */
        /** @var JournalEntry $entry2 */
        $entry1 = $this->prophesize(JournalEntry::class);
        $entry2 = $this->prophesize(JournalEntry::class);
        $entry1
            ->file()
            ->willReturn($file1);
        $entry2
            ->file()
            ->willReturn($file1);
        $this->compiler_prophecy
            ->compile($this->file_source_prophecy, '/path/to/files')
            ->willReturn([$entry1->reveal(), $entry2->reveal()]);

        // create command, execute (using a symfony command tester to simplify the I/O process)
        $command = new CompileAnnotationsCommand($this->file_source_prophecy->reveal(), $this->compiler_prophecy->reveal(), $this->annotation_repository_prophecy->reveal());
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => '/path/to/files'
        ]);

        // the journal entries found should be written to the repository
        $this->annotation_repository_prophecy
            ->record('/path/to/file/1', $entry1)
            ->shouldHaveBeenCalled();
        $this->annotation_repository_prophecy
            ->record('/path/to/file/1', $entry2)
            ->shouldHaveBeenCalled();
    }
}