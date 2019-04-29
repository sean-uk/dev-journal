<?php

namespace App\Service;

/**
 * Class Time
 *
 * This is a very simple utility service for getting date/times.
 * It exists really to be an injectable dependency so the time can be manipulated in other services for test purposes
 *
 * @package App\Service\Time
 */
class Time implements TimeSource
{
    /**
     * Get the current DateTime
     * @return \DateTime
     * @todo is there any meaningful way this can be tested iself?
     */
    public function now() : \DateTime
    {
        $now = new \DateTime('now');
        return $now;
    }
}