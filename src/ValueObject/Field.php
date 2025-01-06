<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\ValueObject;

use Exception;

final class Field
{
    public function __construct(
        private readonly int $initial,
        private readonly int $final,
        private string $value = '',
    ) {
        if ($this->initial > 238) {
            throw new Exception('$initial ou $final ultrapassam o limite máximo de 240');
        }

        if ($this->final > 240) {
            throw new Exception('$initial ou $final ultrapassam o limite máximo de 240');
        }

        if ($this->final < $this->initial) {
            throw new Exception('$initial é maior que o $final');
        }

        $t = $this->final - ($this->initial - 1);

        if (strlen($this->value) > $t) {
            throw new Exception(sprintf('String $value maior que o tamanho definido em $initial e $final: $value=%s e tamanho é de: %s', strlen($this->value), $t));
        }

        $this->value = sprintf("%{$t}s", $this->value);
    }

    public function getInitial(): int
    {
        return $this->initial;
    }

    public function getFinal(): int
    {
        return $this->final;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
