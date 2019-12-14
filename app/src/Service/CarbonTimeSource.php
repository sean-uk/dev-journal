<?php

namespace App\Service;

use Carbon\Carbon;

class CarbonTimeSource implements TimeSource
{
    public function now(): \DateTimeInterface
    {
        return Carbon::now();
    }
}