<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Shipping;

use DateTimeInterface;
use EdineiValdameri\Pagamentos\Enum\Bank;
use EdineiValdameri\Pagamentos\Interface\Generate;
use EdineiValdameri\Pagamentos\Interface\Payer;
use EdineiValdameri\Pagamentos\Interface\Payment;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class AbstractShipping implements Generate
{
    protected string $id;

    protected string $endLine = "\n";

    protected int $positions;

    protected array $wallets = [];

    protected string $wallet;

    protected Bank $bank;

    protected DateTimeInterface $shippingDate;

    /**
     * @var Collection<int, Payment>
     */
    protected Collection $payments;

    public function __construct(
        protected Payer $payer,
    ) {
        $this->payments = collect();
    }

    public function setWallet(string $wallet): void
    {
        if (!in_array($wallet, $this->wallets, true)) {
            throw new InvalidArgumentException('Invalid wallet');
        }

        $this->wallet = $wallet;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setShippingDate(DateTimeInterface $shippingDate): void
    {
        $this->shippingDate = $shippingDate;
    }

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

    abstract protected function header(): string;

    abstract protected function trailer(): string;
}
