<?php

namespace App\JournalAnnotation;

use App\Entity\JournalEntry;

interface Parser
{
    /**
     * Parse a comment string string into a representation of the journal entries in them
     *
     * @param string $comment
     * @return JournalEntry[]
     */
    public function parse(string $comment) : array;
}