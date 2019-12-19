<?php

namespace App\JournalAnnotation;

use App\Entity\JournalEntry;

class PhpCommentParser implements Parser
{
    /**
     * Parse a php comment for journal entries
     *
     * @param string $comment
     * @return JournalEntry[]
     */
    public function parse(string $comment): array
    {
        return [];
    }
}