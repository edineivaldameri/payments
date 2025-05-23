<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Receipt;

use EdineiValdameri\Payments\Enum\Bank;
use EdineiValdameri\Payments\Helper\Useful;
use EdineiValdameri\Payments\Interface\Payer;
use EdineiValdameri\Payments\Interface\Payment;
use EdineiValdameri\Payments\Interface\Process;
use EdineiValdameri\Payments\ValueObject\Field;
use Exception;
use Illuminate\Support\Collection;

abstract class AbstractReceipt implements Process
{
    protected Bank $bank;

    protected array $file;

    protected string $endLine = "\n";

    /**
     * @var Collection<int, Payment>
     */
    protected Collection $payments;

    public function __construct(
        protected Payer $payer,
        mixed $file,
    ) {
        $return = Useful::fileToArray($file);

        if (is_array($return)) {
            $this->file = $return;

            return;
        }

        throw new Exception('File not found');
    }

    abstract public function processHeader(string $data): void;

    abstract public function processTrailer(string $data): void;

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments->push($payment);
    }

    protected function getValue(int $i, int $f, string $array): Field
    {
        return new Field(
            initial: $i,
            final: $f,
            value: substr($array, $i - 1, $f - $i + 1)
        );
    }
}
