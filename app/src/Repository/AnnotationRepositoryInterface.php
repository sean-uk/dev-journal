<?php


namespace App\Repository;

use App\Entity\JournalEntry;

/**
 * Interface EntityRepositoryInterface
 *
 * @package App\Repository
 */
interface AnnotationRepositoryInterface
{
    /**
     * Find all annotations available
     *
     * @return JournalEntry[]
     */
    public function find() : array;

    /**
     * Record an annotation under a given filesystem path
     * @param string $path
     * @param JournalEntry $journalEntry
     */
    public function record(string $path, JournalEntry $journalEntry) : void;
}