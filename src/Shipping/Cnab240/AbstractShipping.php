<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Shipping\Cnab240;

use EdineiValdameri\Pagamentos\Interface\Payment;
use EdineiValdameri\Pagamentos\Shipping\AbstractShipping as AbstractRemessaAlias;

abstract class AbstractShipping extends AbstractRemessaAlias
{
    protected int $positions = 240;

    protected float $amount = 0.00;

    public function generate(): string
    {
        $shipping = $this->header() . $this->endLine;

        $shipping .= $this->headerBatch() . $this->endLine;

        $i = 1;
        foreach ($this->getPayments() as $payment) {
            /** @var Payment $payment */
            $this->amount += $payment->getAmount();
            $shipping .= $this->detail($payment, $i) . $this->endLine;
            $i++;
        }

        $shipping .= $this->trailerBatch() . $this->endLine;

        $shipping .= $this->trailer() . $this->endLine;

        return $shipping;
    }

    abstract protected function headerBatch(): string;

    abstract protected function trailerBatch(): string;

    abstract protected function detail(Payment $payment, int $sequence): string;
}
