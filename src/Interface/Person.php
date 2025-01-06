<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Interface;

interface Person
{
    public function getId(): int;

    public function getDocument(): string;

    public function getName(): string;

    public function getAccount(): BankAccount;

    public function getAddress(): Address;
}
