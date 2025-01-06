<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Interface;

use EdineiValdameri\Payments\Enum\Bank;

interface BankAccount
{
    public function getBank(): Bank;

    public function getAgency(): string;

    public function getAgencyDigit(): ?string;

    public function getAccount(): string;

    public function getAccountDigit(): string;
}
