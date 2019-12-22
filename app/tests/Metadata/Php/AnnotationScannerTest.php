<?php

namespace App\Tests\Metadata;

use App\Filesystem;
use App\Metadata\Comment;
use App\Metadata\Php;
use PhpParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
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

    /** @var Php\CommentVisitor $comment_visitor_prophecy */
    private $comment_visitor_prophecy;

    public function setUp(): void
    {
        parent::setUp();
        $this->file_source_prophecy = $this->prophesize(Filesystem\SourceInterface::class);
        $this->php_parser_prophecy = $this->prophesize(PhpParser\Parser::class);
        $this->comment_visitor_prophecy = $this->prophesize(Php\CommentVisitor::class);
    }

    /**
     * @todo too much test setup going on here.
     */
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
        $nodeProphecy = $this->prophesize(PhpParser\Node::class);
        $nodeProphecy
            ->getSubNodeNames()
            ->willReturn([]);
        $node = $nodeProphecy->reveal();
        $this->php_parser_prophecy
            ->parse('php-code')
            ->willReturn([$node]);

        // the parsed AST should be traversed for comments
        /** @var Comment $comment1 */
        /** @var Comment $comment2 */
        $comment1 = $this->prophesize(PhpParser\Comment::class);
        $comment1
            ->getText()
            ->willReturn('some-journal-annotation-stuff');
        $comment2 = $this->prophesize(PhpParser\Comment\Doc::class);
        $comment2
            ->getText()
            ->willReturn('more-annotation-stuff');

        $this->comment_visitor_prophecy
            ->beforeTraverse([$node])
            ->shouldBeCalled();
        $this->comment_visitor_prophecy
            ->afterTraverse([$node])
            ->shouldBeCalled();
        $this->comment_visitor_prophecy
            ->enterNode($node)
            ->shouldBeCalled();
        $this->comment_visitor_prophecy
            ->leaveNode($node)
            ->shouldBeCalled();
        $this->comment_visitor_prophecy
            ->comments()
            ->willReturn([$comment1->reveal(), $comment2->reveal()]);

        // create scanner
        $scanner = new Php\AnnotationScanner(
            $this->file_source_prophecy->reveal(),
            $this->php_parser_prophecy->reveal(),
            $this->comment_visitor_prophecy->reveal()
        );

        // try to get the comments
        $result = $scanner->comments($fileInfo->reveal());

        // the result should be comment entities with the content of the node comments traversed
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