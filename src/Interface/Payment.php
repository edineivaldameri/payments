<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Interface;

use DateTime;
use EdineiValdameri\Payments\Enum\Finality;
use EdineiValdameri\Payments\Enum\Status;

interface Payment
{
    public function getId(): int;

    public function getReceiver(): Receiver;

    public function getDate(): DateTime;

    public function getFinality(): Finality;

    public function getStatus(): Status;

    public function getAmount(): float;

    public function getDiscountAmount(): float;

    public function getInterestAmount(): float;

    public function getFineAmount(): float;

    public function getAbatementAmount(): float;
}
