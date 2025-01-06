<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Helper;

final class CalculationDV
{
    public static function bradescoAgency(string $agency): int
    {
        /** @var int $digit */
        $digit = Useful::modulo11(
            value: $agency,
            resto10: 'P'
        );

        return $digit;
    }

    public static function bradescoAccount(string $account): int
    {
        /** @var int $digit */
        $digit = Useful::modulo11(
            value: $account,
            resto10: 'P'
        );

        return $digit;
    }
}
