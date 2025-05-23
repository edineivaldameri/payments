<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Receipt\Cnab240;

use DateTime;
use EdineiValdameri\Payments\Enum\Finality;
use EdineiValdameri\Payments\Enum\Status;
use EdineiValdameri\Payments\Interface\Payment;
use EdineiValdameri\Payments\Interface\Receiver;
use Exception;

class Detail implements Payment
{
    private int $id;

    private Status $status;

    private string $occurrence;

    private string $message;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function setOccurrence(string $occurrence): void
    {
        $this->occurrence = $occurrence;
    }

    public function getOccurrence(): string
    {
        return $this->occurrence;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReceiver(): Receiver
    {
        throw new Exception('Method not implemented.');
    }

    public function getDate(): DateTime
    {
        return new DateTime();
    }

    public function getFinality(): Finality
    {
        return Finality::CREDITO_CONTA;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getAmount(): float
    {
        return 0.0;
    }

    public function getDiscountAmount(): float
    {
        return 0.0;
    }

    public function getInterestAmount(): float
    {
        return 0.0;
    }

    public function getFineAmount(): float
    {
        return 0.0;
    }

    public function getAbatementAmount(): float
    {
        return 0.0;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
