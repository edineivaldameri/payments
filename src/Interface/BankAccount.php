<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Interface;

use EdineiValdameri\Pagamentos\Enum\Bank;

interface BankAccount
{
    public function getBank(): Bank;

    public function getAgency(): string;

    public function getAgencyDigit(): ?string;

    public function getAccount(): string;

    public function getAccountDigit(): string;
}
