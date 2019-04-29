<?php

namespace App\Service;

interface TimeSource
{
    public function now() : \DateTime;
}