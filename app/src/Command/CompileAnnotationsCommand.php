<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Metadata\JournalMetadata;

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

    protected function configure()
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'The filesystem path to compile annotations from');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello World!');
    }
}