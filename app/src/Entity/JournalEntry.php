<?php


namespace App\Entity;

use App\Filesystem\FileInfoInterface;
use App\Metadata\Comment;

/**
 * Class JournalEntry
 *
 * A journal entry compiled from a particular place at a particular time.
 *
 * @package App\Entity
 */
class JournalEntry
{
    /**
     * Get the raw string value of the entry
     *
     * @return string
     * @todo
     */
    public function body() : string
    {
    }

    /**
     * The file the journal entry was sourced from
     * @return FileInfoInterface
     */
    public function file() : FileInfoInterface
    {
    }
}