<?php

namespace App\Metadata;

class Comment
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * Get the raw content of the journal entry
     * @return string
     */
    public function content() : string
    {
        return $this->content;
    }
}