<?php

namespace App\Tests\Metadata;

use App\Filesystem;
use App\Metadata\Comment;
use App\Metadata\Php;
use PhpParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class AnnotationScannerTest
 *
 * @see https://github.com/nikic/PHP-Parser/blob/v4.3.0/doc/component/Walking_the_AST.markdown
 * @package App\Tests\Metadata
 */
class AnnotationScannerTest extends TestCase
{
    /** @var Filesystem\SourceInterface $file_source_prophecy */
    private $file_source_prophecy;

    /** @var PhpParser\Parser $php_parser_prophecy */
    private $php_parser_prophecy;

    /** @var PhpParser\NodeFinder $none_finder_prophecy */
    private $none_finder_prophecy;

    /** @var PhpParser\PrettyPrinterAbstract $printer_prophecy */
    private $printer_prophecy;

    public function setUp(): void
    {
        parent::setUp();
        $this->file_source_prophecy = $this->prophesize(Filesystem\SourceInterface::class);
        $this->php_parser_prophecy = $this->prophesize(PhpParser\Parser::class);
        $this->none_finder_prophecy = $this->prophesize(PhpParser\NodeFinder::class);
        $this->printer_prophecy = $this->prophesize(PhpParser\PrettyPrinterAbstract::class);
    }

    public function test_comments() : void
    {
        // rig up a file into object for the path to be scanned
        /** @var Filesystem\FileInfo $fileInfo */
        $fileInfo = $this->prophesize(Filesystem\FileInfo::class);
        $fileInfo
            ->path()
            ->willReturn('path-to-some-file');

        // rig the stub file source to give content for the file's path
        $this->file_source_prophecy
            ->content('path-to-some-file')
            ->willReturn('php-code');

        // the PHP parser will need to parse the 'php-code' into an AST
        /** @var PhpParser\Node $node */
        $node1 = $this->prophesize(PhpParser\Node::class)->reveal();
        $node2 = $this->prophesize(PhpParser\Node::class)->reveal();
        $node3 = $this->prophesize(PhpParser\Node::class)->reveal();
        $this->php_parser_prophecy
            ->parse('php-code')
            ->willReturn([$node1, $node2, $node3]);

        // all the comments should be extracted
        $this->none_finder_prophecy
            ->findInstanceOf([$node1, $node2, $node3], PhpParser\Comment::class)
            ->willReturn([$node2, $node3]);

        // setup some stub content for the comment nodes node
        $this->printer_prophecy
            ->prettyPrint([$node2])
            ->willReturn('some-journal-annotation-stuff');
        $this->printer_prophecy
            ->prettyPrint([$node3])
            ->willReturn('more-annotation-stuff');

        // create scanner
        $scanner = new Php\AnnotationScanner(
            $this->file_source_prophecy->reveal(),
            $this->php_parser_prophecy->reveal(),
            $this->none_finder_prophecy->reveal(),
            $this->printer_prophecy->reveal()
        );

        // try to get the comments
        $result = $scanner->comments($fileInfo->reveal());

        // the result should be a journal metadata item with the content of the node traversed
        $this->assertCount(2, $result);
        $this->assertNotEmpty(array_filter($result, function (Comment $comment) {
            return $comment->content() === 'some-journal-annotation-stuff';
        }));
        $this->assertNotEmpty(array_filter($result, function (Comment $comment) {
            return $comment->content() === 'more-annotation-stuff';
        }));
    }

    // todo: source code error case(s)
    // todo: annotation format error case(s)
}