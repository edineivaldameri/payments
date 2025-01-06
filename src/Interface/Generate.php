<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Interface;

interface Generate
{
    public function generate(): string;
}
