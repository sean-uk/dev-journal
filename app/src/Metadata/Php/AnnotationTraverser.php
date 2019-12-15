<?php

namespace App\Metadata\Php;

use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

/**
 * Class AnnotationTraverser
 *
 * @package App\Metadata\Php
 */
class AnnotationTraverser extends NodeTraverser
{
    /** @var NodeTraverserInterface $traverser */
    private $traverser;

    public function __construct(AnnotationVisitor $annotationVisitor)
    {
        parent::__construct();
        $this->addVisitor($annotationVisitor);
    }
}