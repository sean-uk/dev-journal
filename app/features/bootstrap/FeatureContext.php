<?php

require_once __DIR__ .'/../../vendor/autoload.php';

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given the date-time is :dateTimeString
     */
    public function theDateTimeIs($dateTimeString)
    {
        throw new PendingException();
    }

    /**
     * @Given there is a file (with the path) :path
     */
    public function thereIsAFileCalled($path)
    {
        throw new PendingException();
    }

    /**
     * @Given the file :path has a journal annotation saying:
     */
    public function theFileHasAJournalAnnotationSaying($path, PyStringNode $annotationContent)
    {
        throw new PendingException();
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
}
