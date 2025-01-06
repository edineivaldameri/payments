<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Interface;

interface Generate
{
    public function generate(): string;
}
