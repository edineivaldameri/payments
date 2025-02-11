<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Shipping\Cnab240\Bank;

use EdineiValdameri\Payments\Enum\Bank;
use EdineiValdameri\Payments\Enum\Finality;
use EdineiValdameri\Payments\Enum\Status;
use EdineiValdameri\Payments\Helper\CalculationDV;
use EdineiValdameri\Payments\Helper\Useful;
use EdineiValdameri\Payments\Interface\Payer;
use EdineiValdameri\Payments\Interface\Payment;
use EdineiValdameri\Payments\Shipping\Cnab240\AbstractShipping;
use EdineiValdameri\Payments\ValueObject\Field;
use EdineiValdameri\Payments\ValueObject\Header;
use EdineiValdameri\Payments\ValueObject\HeaderBatch;
use EdineiValdameri\Payments\ValueObject\Segment;
use EdineiValdameri\Payments\ValueObject\Trailer;
use EdineiValdameri\Payments\ValueObject\TrailerBatch;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Bradesco extends AbstractShipping
{
    protected array $wallets = ['09'];

    protected string $endLine = "\r\n";

    private string $clientCode;

    public function __construct(Payer $payer)
    {
        parent::__construct($payer);
        $this->bank = Bank::BRADESCO;
    }

    public function setClientCode(string $clientCode): void
    {
        $this->clientCode = $clientCode;
    }

    protected function header(): string
    {
        $header = new Header();
        $header->addField(new Field(1, 3, $this->bank->value));
        $header->addField(new Field(4, 7, '0000'));
        $header->addField(new Field(8, 8, '0'));
        $header->addField(new Field(9, 17));
        $header->addField(new Field(18, 18, strlen(Useful::onlyNumbers($this->payer->getDocument())) === 14 ? '2' : '1'));
        $header->addField(new Field(19, 32, Useful::formatCnab('9', Useful::onlyNumbers($this->payer->getDocument()), 14)));
        $header->addField(new Field(33, 52, Useful::formatCnab('X', Useful::onlyNumbers($this->getClientCode()), 20)));
        $header->addField(new Field(53, 57, Useful::formatCnab('9', $this->payer->getAccount()->getAgency(), 5)));
        $header->addField(new Field(58, 58, (string) CalculationDV::bradescoAgency($this->payer->getAccount()->getAgency())));
        $header->addField(new Field(59, 70, Useful::formatCnab('9', $this->payer->getAccount()->getAccount(), 12)));
        $header->addField(new Field(71, 71, (string) CalculationDV::bradescoAccount($this->payer->getAccount()->getAccount())));
        $header->addField(new Field(72, 72));
        $header->addField(new Field(73, 102, Useful::formatCnab('X', $this->payer->getName(), 30)));
        $header->addField(new Field(103, 132, Useful::formatCnab('X', $this->bank->name, 30)));
        $header->addField(new Field(133, 142));
        $header->addField(new Field(143, 143, '1'));
        $header->addField(new Field(144, 151, $this->shippingDate->format('dmY')));
        $header->addField(new Field(152, 157, $this->shippingDate->format('His')));
        $header->addField(new Field(158, 163, Useful::formatCnab('9', $this->id, 6)));
        $header->addField(new Field(164, 166, '089'));
        $header->addField(new Field(167, 171, '01600'));
        $header->addField(new Field(172, 174));
        $header->addField(new Field(175, 191));
        $header->addField(new Field(192, 211));
        $header->addField(new Field(212, 240));

        return $header->generate();
    }

    protected function headerBatch(): string
    {
        $headerBatch = new HeaderBatch();
        $headerBatch->addField(new Field(1, 3, Useful::formatCnab('9', $this->bank->value, 3)));
        $headerBatch->addField(new Field(4, 7, '0001'));
        $headerBatch->addField(new Field(8, 8, '1'));
        $headerBatch->addField(new Field(9, 9, 'C'));
        $headerBatch->addField(new Field(10, 11, '30'));
        $headerBatch->addField(new Field(12, 13, '01'));
        $headerBatch->addField(new Field(14, 16, '045'));
        $headerBatch->addField(new Field(17, 17));
        $headerBatch->addField(new Field(18, 18, strlen(Useful::onlyNumbers($this->payer->getDocument())) === 14 ? '2' : '1'));
        $headerBatch->addField(new Field(19, 32, Useful::formatCnab('9', Useful::onlyNumbers($this->payer->getDocument()), 14)));
        $headerBatch->addField(new Field(33, 52, Useful::formatCnab('X', Useful::onlyNumbers($this->getClientCode()), 20)));
        $headerBatch->addField(new Field(53, 57, Useful::formatCnab('9', $this->payer->getAccount()->getAgency(), 5)));
        $headerBatch->addField(new Field(58, 58, (string) CalculationDV::bradescoAgency($this->payer->getAccount()->getAgency())));
        $headerBatch->addField(new Field(59, 70, Useful::formatCnab('9', $this->payer->getAccount()->getAccount(), 12)));
        $headerBatch->addField(new Field(71, 71, (string) CalculationDV::bradescoAccount($this->payer->getAccount()->getAccount())));
        $headerBatch->addField(new Field(72, 72));
        $headerBatch->addField(new Field(73, 102, Useful::formatCnab('X', $this->payer->getName(), 30)));
        $headerBatch->addField(new Field(103, 142));
        $headerBatch->addField(new Field(143, 172, Useful::formatCnab('X', $this->payer->getAddress()->getStreet(), 30)));
        $headerBatch->addField(new Field(173, 177, Useful::formatCnab('9', $this->payer->getAddress()->getNumber(), 5)));
        $headerBatch->addField(new Field(178, 192, Useful::formatCnab('X', $this->payer->getAddress()->getComplement(), 15)));
        $headerBatch->addField(new Field(193, 212, Useful::formatCnab('X', $this->payer->getAddress()->getCity(), 20)));
        $headerBatch->addField(new Field(213, 217, Useful::formatCnab('9', Useful::onlyNumbers($this->payer->getAddress()->getZipCode()), 5)));
        $headerBatch->addField(new Field(218, 220, Useful::formatCnab('9', Useful::onlyNumbers(substr($this->payer->getAddress()->getZipCode(), -3)), 3)));
        $headerBatch->addField(new Field(221, 222, Useful::formatCnab('X', $this->payer->getAddress()->getState(), 2)));
        $headerBatch->addField(new Field(223, 224, '01'));
        $headerBatch->addField(new Field(225, 230));
        $headerBatch->addField(new Field(231, 240));

        return $headerBatch->generate();
    }

    protected function detail(Payment $payment): string
    {
        $detail = $this->segmentA($payment, $this->batchSequence) . $this->endLine;
        $this->batchSequence++;
        $detail .= $this->segmentB($payment, $this->batchSequence);
        $this->batchSequence++;

        return $detail;
    }

    protected function trailerBatch(): string
    {
        $trailerBatch = new TrailerBatch();
        $trailerBatch->addField(new Field(1, 3, Useful::formatCnab('9', $this->bank->value, 3)));
        $trailerBatch->addField(new Field(4, 7, '0001'));
        $trailerBatch->addField(new Field(8, 8, '5'));
        $trailerBatch->addField(new Field(9, 17));
        $trailerBatch->addField(new Field(18, 23, Useful::formatCnab('9', (string) ($this->batchSequence + 1), 6)));
        $trailerBatch->addField(new Field(24, 41, Useful::formatCnab('9', (string) $this->amount, 18, 2)));
        $trailerBatch->addField(new Field(42, 59, '000000000000000000'));
        $trailerBatch->addField(new Field(60, 65, '000000'));
        $trailerBatch->addField(new Field(66, 230));
        $trailerBatch->addField(new Field(231, 240));

        return $trailerBatch->generate();
    }

    protected function trailer(): string
    {
        $trailer = new Trailer();
        $trailer->addField(new Field(1, 3, Useful::formatCnab('9', $this->bank->value, 3)));
        $trailer->addField(new Field(4, 7, '9999'));
        $trailer->addField(new Field(8, 8, '9'));
        $trailer->addField(new Field(9, 17));
        $trailer->addField(new Field(18, 23, Useful::formatCnab('9', '1', 6)));
        $trailer->addField(new Field(24, 29, Useful::formatCnab('9', (string) ($this->batchSequence + 3), 6)));
        $trailer->addField(new Field(30, 35, '000000'));
        $trailer->addField(new Field(36, 240));

        return $trailer->generate();
    }

    private function segmentA(Payment $payment, int $sequence): string
    {
        $segment = new Segment();
        $segment->addField(new Field(1, 3, Useful::formatCnab('9', $this->bank->value, 3)));
        $segment->addField(new Field(4, 7, '0001'));
        $segment->addField(new Field(8, 8, '3'));
        $segment->addField(new Field(9, 13, Useful::formatCnab('9', (string) $sequence, 5)));
        $segment->addField(new Field(14, 14, 'A'));
        $segment->addField(new Field(15, 15, '0'));
        $segment->addField(new Field(16, 17, $this->getStatus($payment->getStatus())));
        $segment->addField(new Field(18, 20, '000'));
        $segment->addField(new Field(21, 23, Useful::formatCnab('9', $payment->getReceiver()->getAccount()->getBank()->value, 3)));
        $segment->addField(new Field(24, 28, Useful::formatCnab('9', $payment->getReceiver()->getAccount()->getAgency(), 5)));
        $agencyDigit = '';
        if ($payment->getReceiver()->getAccount()->getAgencyDigit()) {
            $agencyDigit = $payment->getReceiver()->getAccount()->getAgencyDigit();
        }
        $segment->addField(new Field(29, 29, Useful::formatCnab('9', $agencyDigit, 1)));
        $segment->addField(new Field(30, 41, Useful::formatCnab('9', $payment->getReceiver()->getAccount()->getAccount(), 12)));
        $segment->addField(new Field(42, 42, Useful::formatCnab('9', $payment->getReceiver()->getAccount()->getAccountDigit(), 1)));
        $segment->addField(new Field(43, 43));
        $segment->addField(new Field(44, 73, Useful::formatCnab('X', $payment->getReceiver()->getName(), 30)));
        $segment->addField(new Field(74, 93, Useful::formatCnab('9', (string) $payment->getId(), 20)));
        $segment->addField(new Field(94, 101, $payment->getDate()->format('dmY')));
        $segment->addField(new Field(102, 104, 'BRL'));
        $segment->addField(new Field(105, 119, Useful::formatCnab('9', (string) $payment->getAmount(), 15, 5)));
        $segment->addField(new Field(120, 134, Useful::formatCnab('9', (string) $payment->getAmount(), 15, 2)));
        $segment->addField(new Field(135, 154));
        $segment->addField(new Field(155, 162, '00000000'));
        $segment->addField(new Field(163, 177, '000000000000000'));
        $segment->addField(new Field(178, 217, ''));
        $segment->addField(new Field(218, 219, '06'));
        $segment->addField(new Field(220, 224, $this->getFinality($payment->getFinality())));
        $segment->addField(new Field(225, 226));
        $segment->addField(new Field(227, 229));
        $segment->addField(new Field(230, 230, '0'));
        $segment->addField(new Field(231, 240));

        return $segment->generate();
    }

    private function segmentB(Payment $payment, int $sequence): string
    {
        $segment = new Segment();
        $segment->addField(new Field(1, 3, Useful::formatCnab('9', $this->bank->value, 3)));
        $segment->addField(new Field(4, 7, '0001'));
        $segment->addField(new Field(8, 8, '3'));
        $segment->addField(new Field(9, 13, Useful::formatCnab('9', (string) $sequence, 5)));
        $segment->addField(new Field(14, 14, 'B'));
        $segment->addField(new Field(15, 17));
        $segment->addField(new Field(18, 18, strlen(Useful::onlyNumbers($payment->getReceiver()->getDocument())) === 14 ? '2' : '1'));
        $segment->addField(new Field(19, 32, Useful::formatCnab('9', Useful::onlyNumbers($payment->getReceiver()->getDocument()), 14)));
        $segment->addField(new Field(33, 62, Useful::formatCnab('X', $payment->getReceiver()->getAddress()->getStreet(), 30)));
        $segment->addField(new Field(63, 67, Useful::formatCnab('9', $payment->getReceiver()->getAddress()->getNumber(), 5)));
        $segment->addField(new Field(68, 82, Useful::formatCnab('X', $payment->getReceiver()->getAddress()->getComplement(), 15)));
        $segment->addField(new Field(83, 97, Useful::formatCnab('X', $payment->getReceiver()->getAddress()->getNeighborhood(), 15)));
        $segment->addField(new Field(98, 117, Useful::formatCnab('X', $payment->getReceiver()->getAddress()->getCity(), 20)));
        $segment->addField(new Field(118, 122, Useful::formatCnab('9', Useful::onlyNumbers($payment->getReceiver()->getAddress()->getZipCode()), 5)));
        $segment->addField(new Field(123, 125, Useful::formatCnab('9', Useful::onlyNumbers(substr($payment->getReceiver()->getAddress()->getZipCode(), -3)), 3)));
        $segment->addField(new Field(126, 127, Useful::formatCnab('X', $payment->getReceiver()->getAddress()->getState(), 2)));
        $segment->addField(new Field(128, 135, $payment->getDate()->format('dmY')));
        $segment->addField(new Field(136, 150, Useful::formatCnab('9', (string) $payment->getAmount(), 15, 2)));
        $segment->addField(new Field(151, 165, Useful::formatCnab('9', (string) $payment->getAbatementAmount(), 15, 2)));
        $segment->addField(new Field(166, 180, Useful::formatCnab('9', (string) $payment->getDiscountAmount(), 15, 2)));
        $segment->addField(new Field(181, 195, Useful::formatCnab('9', (string) $payment->getInterestAmount(), 15, 2)));
        $segment->addField(new Field(196, 210, Useful::formatCnab('9', (string) $payment->getFineAmount(), 15, 2)));
        $segment->addField(new Field(211, 225, Useful::formatCnab('X', (string) $payment->getReceiver()->getId(), 15)));
        $segment->addField(new Field(226, 226, '0'));
        $segment->addField(new Field(227, 232));
        $segment->addField(new Field(233, 240));

        return $segment->generate();
    }

    private function getClientCode(): string
    {
        if (empty($this->clientCode)) {
            $this->clientCode = Useful::formatCnab('9', $this->wallet, 4) .
                Useful::formatCnab('9', $this->payer->getAccount()->getAgency(), 5) .
                Useful::formatCnab('9', $this->payer->getAccount()->getAccount(), 7) .
                Useful::formatCnab('9', $this->payer->getAccount()->getAccountDigit(), 1);
        }

        return $this->clientCode;
    }

    private function getStatus(Status $status): string
    {
        return match ($status) {
            Status::ALTERACAO => '17',
            Status::BAIXA => '27',
            Status::ALTERACAO_DATA => '19',
            default => '00',
        };
    }

    private function getFinality(Finality $finality): string
    {
        return match ($finality) {
            Finality::PAGAMENTO_IMPOSTOS_E_TAXAS => '0001',
            Finality::PAGAMENTO_CONCESSIONARIAS => '0002',
            Finality::PAGAMENTO_DIVIDENDENDOS => '0003',
            Finality::PAGAMENTO_SALARIOS => '0004',
            Finality::PAGAMENTO_FORNECEDORES => '0005',
            Finality::PAGAMENTO_HONORARIOS => '0006',
            Finality::PAGAMENTO_ALUGUEIS_E_CONDOMINIOS => '0007',
            Finality::PAGAMENTO_DUPLICATAS_E_TITULOS => '0008',
            Finality::PAGAMENTO_MENSALIDADE_ESCOLAR => '0009',
            Finality::CREDITO_CONTA => '0010',
        };
    }
}
