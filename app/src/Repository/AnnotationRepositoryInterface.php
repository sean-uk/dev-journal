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
     * @param string $path
     * @param \DateTime $date
     * @return JournalEntry[]
     */
    public function find(string $path, \DateTime $date) : array;
}