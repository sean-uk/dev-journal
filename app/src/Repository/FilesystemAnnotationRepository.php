<?php

namespace App\Repository;

use App\Compiler\FilesystemCompiler;
use App\Entity\JournalEntry;
use App\Filesystem;
use App\Metadata;

/**
 * Class FilesystemAnnotationRepository
 *
 * Store annotatinos on (local) filesystem
 *
 * @package App\Repository
 */
class FilesystemAnnotationRepository implements AnnotationRepositoryInterface
{
    /** @var Filesystem\SourceInterface $fileSource */
    private $fileSource;

    /** @var FilesystemCompiler $compiler */
    private $compiler;

    /**
     * FilesystemAnnotationRepository constructor.
     *
     * @param Filesystem\SourceInterface $fileSource;
     * @param FilesystemCompiler $filesystemCompiler
     */
    public function __construct(Filesystem\SourceInterface $fileSource, FilesystemCompiler $filesystemCompiler)
    {
        $this->fileSource = $fileSource;
        $this->compiler = $filesystemCompiler;
    }

    public function find(): array
    {
        // just use the filesystem compiler to re-scan all the entries from the whole file source
        $entries = $this->compiler->compile($this->fileSource);
        return $entries;
    }

    public function record(string $path, JournalEntry $journalEntry): void
    {
        // TODO: Implement record() method.
    }
}