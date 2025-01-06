<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Interface;

interface Address
{
    public function getStreet(): string;

    public function getNumber(): string;

    public function getComplement(): string;

    public function getNeighborhood(): string;

    public function getCity(): string;

    public function getState(): string;

    public function getZipCode(): string;
}
