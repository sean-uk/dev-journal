<?php

namespace App\Behat;

require_once __DIR__ .'/../../vendor/autoload.php';

use App\Command\CompileAnnotationsCommand;
use App\Entity\JournalEntry;
use App\Filesystem\FlysystemSource;
use App\Repository\AnnotationRepositoryInterface;
use App\Repository\FilesystemAnnotationRepository;
use App\Service\TimeSource;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use PHPUnit\Framework\Assert;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Symfony\Component\Console\Tester\CommandTester;
use App\Filesystem\SourceInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var ObjectProphecy $time */
    private $time;

    /** @var FilesystemInterface */
    private $filesystem;

    /** @var string|null $last_command_output */
    private $last_command_output;

    /** @var AnnotationRepositoryInterface $annotation_repository */
    private $annotation_repository;

    /** @var SourceInterface $file_source */
    private $file_source;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        // setup the file source using an in-memory filesystem
        $this->filesystem = $this->bootstrapMemoryFilesystem();
        $this->file_source = new FlysystemSource($this->filesystem);

        // create an annotation repository;
        $this->annotation_repository = new FilesystemAnnotationRepository($this->file_source);

        // create a stub time source so the time can be changed as needed
        $prophet = new Prophet();
        $this->time = $prophet->prophesize(TimeSource::class);
    }

    /**
     * @Given the date-time is :dateTimeString
     */
    public function theDateTimeIs($dateTimeString)
    {
        $this->time->now()->willReturn($dateTimeString);
    }

    /**
     * @Given there is a file (with the path) :path
     */
    public function thereIsAFileCalled($path)
    {
        // just create an empty file
        $this->filesystem->put($path, '');
    }

    /**
     * @Given the file :path has a php journal annotation saying:
     */
    public function theFileHasAPhpJournalAnnotationSaying($path, PyStringNode $annotationContent)
    {
        // formulate the content into an actual journal entry annotation
        $annotationClass = JournalEntry::class;
        $content = <<<EOT
/**
 * $annotationClass(
 *     message = "$annotationContent"
 * )
 */
EOT;
        // just replace the file content
        $this->filesystem->put($path, $content);
    }

    /**
     * @When I run the journal annotation compile command on the path :path
     */
    public function iRunTheJournalAnnotationCompileCommand($path)
    {
        $this->last_command_output = $this->runAnnotationCompileCommand($path);
    }

    /**
     * @Then there should be an annotation compiled from :path at :dateTimeString saying:
     */
    public function thereShouldBeAnAnnotationCompiledFromAtSaying($path, $dateTimeString, PyStringNode $annotationContent)
    {
        // get all annotations and check for one with the text in question
        $found = $this->annotation_repository->find($path);
        Assert::assertCount(1, $found);
        Assert::assertEquals($annotationContent, $found[0]->content());
    }

    /**
     * @see https://symfony.com/doc/current/console.html#testing-commands
     */
    private function runAnnotationCompileCommand(string $path) : string
    {
        // use a command tester to execute the command
        $command = new CompileAnnotationsCommand($this->annotation_repository, 'thingy');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => $path
        ]);

        // return the command output for later assertations
        return $commandTester->getDisplay();
    }

    /**
     * This creates a new in-memory mock filesystem
     *
     * @see https://flysystem.thephpleague.com/docs/adapter/memory/
     * @return FilesystemInterface
     */
    private function bootstrapMemoryFilesystem() : FilesystemInterface
    {
        $adapter = new MemoryAdapter();
        $filesystem = $this->filesystem = new Filesystem($adapter);
        return $filesystem;
    }
}
