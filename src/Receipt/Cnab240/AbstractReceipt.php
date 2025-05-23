<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Receipt\Cnab240;

use EdineiValdameri\Payments\Receipt\AbstractReceipt as AbstractRetornoAlias;
use Exception;

abstract class AbstractReceipt extends AbstractRetornoAlias
{
    protected int $positions = 240;

    public function process(): void
    {
        foreach ($this->file as $line) {
            $line = str_replace($this->endLine, '', $line);
            if (strlen($line) !== $this->positions) {
                throw new Exception('Invalid line length');
            }

            $type = $this->getValue(8, 8, $line);

            match ($type->getValue()) {
                '0' => $this->processHeader($line),
                '1' => $this->processHeaderBatch($line),
                '3' => $this->processDetail($line),
                '5' => $this->processTrailerBatch($line),
                '9' => $this->processTrailer($line),
                default => throw new Exception('Invalid line type'),
            };
        }
    }

    abstract public function processHeaderBatch(string $data): void;

    abstract public function processDetail(string $data): void;

    abstract public function processTrailerBatch(string $data): void;
}
