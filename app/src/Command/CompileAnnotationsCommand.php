<?php

namespace App\Command;

use App\Compiler\FilesystemCompiler;
use App\Filesystem\SourceInterface;
use App\Repository\AnnotationRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CompileAnnotationsCommand
 *
 * This command combs source files for {@link JournalAnnotation} comments and saves them.
 *
 * @package App\Command
 */
class CompileAnnotationsCommand extends Command
{
    protected static $defaultName = 'app:journal:compile';

    /** @var SourceInterface $file_source */
    private $file_source;

    /** @var FilesystemCompiler $compiler */
    private $compiler;

    /** @var AnnotationRepositoryInterface $annotation_repository */
    private $annotation_repository;

    /**
     * CompileAnnotationsCommand constructor.
     *
     * @param SourceInterface $fileSource
     * @param FilesystemCompiler $filesystemCompiler
     * @param AnnotationRepositoryInterface $annotationRepository
     * @param string|null $name
     */
    public function __construct(
        SourceInterface $fileSource,
        FilesystemCompiler $filesystemCompiler,
        AnnotationRepositoryInterface $annotationRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->file_source = $fileSource;
        $this->compiler = $filesystemCompiler;
        $this->annotation_repository = $annotationRepository;
    }

    protected function configure()
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'The filesystem path to compile annotations from');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     *
     * @todo rid of the hard binding flysystem
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get the files from the source
        $path = $input->getArgument('path');

        // get the journal entries
        $journalEntries = $this->compiler->compile($this->file_source, $path);

        // store the entries to the repository
        foreach ($journalEntries as $journalEntry) {
            $path = $journalEntry->file()->path();
            $this->annotation_repository->record($path, $journalEntry);
        }
    }
}