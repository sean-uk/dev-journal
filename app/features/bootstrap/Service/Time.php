<?php

namespace App\Behat\Service;

use App\Service\TimeSource;

/**
 * Class Time
 *
 * This is a test version of the real {@link \App\Service\Time\Time} service which can be set to say it's whatever time you want.
 */
class Time implements TimeSource
{
    /** @var \DateTime $fake_time */
    private $fake_time;

    /**
     * Set the fake time which this instance will report it to be when asked.
     *
     * @param \DateTime $time
     */
    public function set(\DateTime $time)
    {
        $this->fake_time = $time;
    }

    /**
     * if a fake date time has been set, that will be returned, otherwise return the real current date time
     *
     * @return \DateTime
     */
    public function now(): \DateTime
    {
        if ($this->fake_time) {
            return $this->fake_time;
        }
        return new \DateTime('now');
    }
}