<?php

namespace App\Command;

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

    /** @var AnnotationRepositoryInterface $annotation_repository */
    private $annotation_repository;

    /**
     * CompileAnnotationsCommand constructor.
     *
     * @param AnnotationRepositoryInterface $annotationRepository
     * @param string|null $name
     */
    public function __construct(AnnotationRepositoryInterface $annotationRepository, string $name = null)
    {
        parent::__construct($name);
        $this->annotation_repository = $annotationRepository;
    }

    protected function configure()
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'The filesystem path to compile annotations from');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}