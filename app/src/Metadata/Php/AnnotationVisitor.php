<?php

namespace App\Metadata\Php;

use PhpParser\Node;
use PhpParser\NodeVisitor;

class AnnotationVisitor implements NodeVisitor
{
    public function leaveNode(Node $node)
    {

    }

    /**
     * @inheritDoc
     */
    public function enterNode(Node $node)
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function beforeTraverse(array $nodes)
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function afterTraverse(array $nodes)
    {
        return;
    }
}