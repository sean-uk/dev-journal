<?php

namespace App\Metadata\Php;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Class CommentVisitor
 *
 * This is here to extract comments from parsed PHP source
 *
 * @package App\Metadata\Php
 */
class CommentVisitor extends NodeVisitorAbstract
{
    /** @var Comment[] $comments */
    private $comments = [];

    /**
     * @param array $nodes
     * @return void
     */
    public function beforeTraverse(array $nodes)
    {
        parent::beforeTraverse($nodes);
        $this->comments = [];
    }

    /**
     * add any comments seen in the node to the collection
     *
     * @param Node $node
     * @return void
     */
    public function leaveNode(Node $node)
    {
        // if the node has comments, add those to this visitor's list
        $comments = $node->getComments();
        foreach ($comments as $comment) {
            $this->comments[] = $comment;
        }
    }

    /**
     * All the comments visited
     * @return Comment[]
     */
    public function comments() : array
    {
        return $this->comments;
    }
}