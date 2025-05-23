<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Interface;

interface Process
{
    public function process(): void;
}
