<?php

namespace App\Behat;

require_once __DIR__ .'/../../vendor/autoload.php';

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use App\Behat\Service\Time;
use App\Kernel;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use App\Annotation\JournalAnnotation;

/**
 * Defines application features from the specific context.
 * @todo have the symfony var/cache dumped between runs?
 */
class FeatureContext implements Context
{
    /** @var Kernel $app */
    private $kernel;

    /** @var Time */
    private $time;

    /** @var FilesystemInterface */
    private $filesystem;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        // bootstrap an app kernel so    it can actually be used to handle requests and inspect output, etc.
        $this->kernel = $this->bootstrapKernel();
        $container = $this->kernel->getContainer();

        // replace the DI container's time service with a riggable mock
        $this->time = new Time();
        $container->set('time', $this->time);

        // setup the mock filesystem
        $this->filesystem = $this->bootstrapMemoryFilesystem();
    }

    /**
     * @Given the date-time is :dateTimeString
     */
    public function theDateTimeIs($dateTimeString)
    {
        $dateTime = new \DateTime($dateTimeString);
        $this->time->set($dateTime);
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
        // formulate the content into an actual php annotation
        $annotationClass = JournalAnnotation::class;
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
        throw new PendingException();
    }

    /**
     * @Then there should be an annotation compiled from :path at :dateTimeString saying:
     */
    public function thereShouldBeAnAnnotationCompiledFromAtSaying($path, $dateTimeString, PyStringNode $annotationContent)
    {
        throw new PendingException();
    }

    /**
     * Get an app kernel bootstrapped into a test environment.
     *
     * This is based on what's in symfony's own app/public/index.php
     * And also what they do in \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase::bootKernel
     *
     * @return \App\Kernel
     */
    private function bootstrapKernel() : Kernel
    {
        $kernel = new Kernel('test', false);
        $kernel->boot();
        return $kernel;
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
