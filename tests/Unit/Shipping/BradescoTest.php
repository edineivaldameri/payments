<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Tests\Unit\Shipping;

use DateTime;
use EdineiValdameri\Payments\Enum\Bank;
use EdineiValdameri\Payments\Enum\Finality;
use EdineiValdameri\Payments\Enum\Status;
use EdineiValdameri\Payments\Interface\Address;
use EdineiValdameri\Payments\Interface\BankAccount;
use EdineiValdameri\Payments\Interface\Payer;
use EdineiValdameri\Payments\Interface\Payment;
use EdineiValdameri\Payments\Interface\Receiver;
use EdineiValdameri\Payments\Shipping\Cnab240\Bank\Bradesco;
use EdineiValdameri\Payments\Tests\TestCase;
use Exception;
use ReflectionMethod;
use ReflectionProperty;

class BradescoTest extends TestCase
{
    private Payer $payer;

    private BankAccount $account;

    private Bradesco $bank;

    private Address $address;

    private Receiver $receiver;

    private Payment $payment;

    private Payment $payment2;

    private string $header = '23700000         21111111100011100091234512345679   1234550000012345679 LOREM IPSUM DOLOR SIT AMET CONBRADESCO                                10101202512595900012308901600                                                                     ';

    private string $headerBatch = '23700011C3001045 21111111100011100091234512345679   1234550000012345679 LOREM IPSUM DOLOR SIT AMET CON                                        LOREM IPSUM                   00123APTO 123       LOREM IPSUM         12345678SP01                ';

    private string $segmentA = '2370001300001A0000002371234550000012345679 LOREM IPSUM DOLOR SIT AMET CON0000000000000000000101012025BRL000000123456000000000000123456                    00000000000000000000000                                        06 0004     0          ';

    private string $segmentB = '2370001300002B   211111111000111LOREM IPSUM                   00123APTO 123       LOREM IPSUM    LOREM IPSUM         12345678SP01012025000000000123456000000000123456000000000123456000000000123456000000000123456123456789      0              ';

    private string $segmentA2 = '2370001300003A0000002371234550000012345679 LOREM IPSUM DOLOR SIT AMET CON0000000000000000000101012025BRL000000654321000000000000654321                    00000000000000000000000                                        06 0004     0          ';

    private string $segmentB2 = '2370001300004B   211111111000111LOREM IPSUM                   00123APTO 123       LOREM IPSUM    LOREM IPSUM         12345678SP01012025000000000654321000000000654321000000000654321000000000654321000000000654321123456789      0              ';

    private string $trailerBatch = '23700015         000006000000000000777777000000000000000000000000                                                                                                                                                                               ';

    private string $trailer = '23799999         000001000008000000                                                                                                                                                                                                             ';

    private string $endLine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = $this->createMock(BankAccount::class);
        $this->account->method('getBank')->willReturn(Bank::BRADESCO);
        $this->account->method('getAgency')->willReturn('12345');
        $this->account->method('getAgencyDigit')->willReturn('5');
        $this->account->method('getAccount')->willReturn('1234567');
        $this->account->method('getAccountDigit')->willReturn('9');

        $this->address = $this->createMock(Address::class);
        $this->address->method('getStreet')->willReturn('LOREM IPSUM');
        $this->address->method('getNumber')->willReturn('123');
        $this->address->method('getComplement')->willReturn('APTO 123');
        $this->address->method('getNeighborhood')->willReturn('LOREM IPSUM');
        $this->address->method('getCity')->willReturn('LOREM IPSUM');
        $this->address->method('getState')->willReturn('SP');
        $this->address->method('getZipCode')->willReturn('12345678');

        $this->payer = $this->createMock(Payer::class);
        $this->payer->method('getAccount')->willReturn($this->account);
        $this->payer->method('getAddress')->willReturn($this->address);
        $this->payer->method('getDocument')->willReturn('11111111000111');
        $this->payer->method('getName')->willReturn('LOREM IPSUM DOLOR SIT AMET CONSECTETUR');

        $this->bank = new Bradesco($this->payer);
        $this->bank->setWallet('09');
        $this->bank->setId('123');
        $this->bank->setShippingDate(new DateTime('2025-01-01 12:59:59'));

        $reflectionProperty = new ReflectionProperty($this->bank, 'endLine');
        $reflectionProperty->setAccessible(true);
        $this->endLine = $reflectionProperty->getValue($this->bank);

        $this->receiver = $this->createMock(Receiver::class);
        $this->receiver->method('getId')->willReturn(123456789);
        $this->receiver->method('getAccount')->willReturn($this->account);
        $this->receiver->method('getAddress')->willReturn($this->address);
        $this->receiver->method('getDocument')->willReturn('11111111000111');
        $this->receiver->method('getName')->willReturn('LOREM IPSUM DOLOR SIT AMET CONSECTETUR');

        $this->payment = $this->createMock(Payment::class);
        $this->payment->method('getId')->willReturn(1);
        $this->payment->method('getReceiver')->willReturn($this->receiver);
        $this->payment->method('getDate')->willReturn(new DateTime('2025-01-01 12:59:59'));
        $this->payment->method('getFinality')->willReturn(Finality::PAGAMENTO_SALARIOS);
        $this->payment->method('getStatus')->willReturn(Status::REGISTRO);
        $this->payment->method('getAmount')->willReturn(1234.56);
        $this->payment->method('getDiscountAmount')->willReturn(1234.56);
        $this->payment->method('getInterestAmount')->willReturn(1234.56);
        $this->payment->method('getFineAmount')->willReturn(1234.56);
        $this->payment->method('getAbatementAmount')->willReturn(1234.56);

        $this->payment2 = $this->createMock(Payment::class);
        $this->payment2->method('getId')->willReturn(1);
        $this->payment2->method('getReceiver')->willReturn($this->receiver);
        $this->payment2->method('getDate')->willReturn(new DateTime('2025-01-01 12:59:59'));
        $this->payment2->method('getFinality')->willReturn(Finality::PAGAMENTO_SALARIOS);
        $this->payment2->method('getStatus')->willReturn(Status::REGISTRO);
        $this->payment2->method('getAmount')->willReturn(6543.21);
        $this->payment2->method('getDiscountAmount')->willReturn(6543.21);
        $this->payment2->method('getInterestAmount')->willReturn(6543.21);
        $this->payment2->method('getFineAmount')->willReturn(6543.21);
        $this->payment2->method('getAbatementAmount')->willReturn(6543.21);

        $this->bank->addPayment($this->payment);
        $this->bank->addPayment($this->payment2);
    }

    public function testShippingNotWallet()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid wallet');
        $bank = new Bradesco($this->payer);
        $bank->setWallet('1');
    }

    public function testGetClientCode()
    {
        $reflectionMethod = new ReflectionMethod($this->bank, 'getClientCode');
        $this->assertEquals('00091234512345679', $reflectionMethod->invoke($this->bank));
    }

    public function testHeader()
    {
        $reflectionMethod = new ReflectionMethod($this->bank, 'header');
        $generated = $reflectionMethod->invoke($this->bank);

        $this->assertEquals($this->header, $generated);
        $this->assertEquals(240, strlen((string) $generated));
    }

    public function testHeaderBatch()
    {
        $reflectionMethod = new ReflectionMethod($this->bank, 'headerBatch');
        $generated = $reflectionMethod->invoke($this->bank);

        $this->assertEquals($this->headerBatch, $generated);
        $this->assertEquals(240, strlen((string) $generated));
    }

    public function testSegmentA()
    {
        $reflectionMethod = new ReflectionMethod($this->bank, 'segmentA');
        $generated = $reflectionMethod->invoke($this->bank, $this->payment, 1);

        $this->assertEquals($this->segmentA, $generated);
        $this->assertEquals(240, strlen((string) $generated));
    }

    public function testSegmentB()
    {
        $uniqueSegmentB = '2370001300001B   211111111000111LOREM IPSUM                   00123APTO 123       LOREM IPSUM    LOREM IPSUM         12345678SP01012025000000000123456000000000123456000000000123456000000000123456000000000123456123456789      0              ';
        $reflectionMethod = new ReflectionMethod($this->bank, 'segmentB');
        $generated = $reflectionMethod->invoke($this->bank, $this->payment, 1);

        $this->assertEquals($uniqueSegmentB, $generated);
        $this->assertEquals(240, strlen((string) $generated));
    }

    public function testDetail()
    {
        $reflectionMethod = new ReflectionMethod($this->bank, 'detail');
        $generated = $reflectionMethod->invoke($this->bank, $this->payment, 1);

        $detail = $this->segmentA .
            $this->endLine .
            $this->segmentB;

        $this->assertEquals($detail, $generated);
        $this->assertEquals(482, strlen((string) $generated));
    }

    public function testTrailerBatch()
    {
        $this->bank->generate();
        $reflectionMethod = new ReflectionMethod($this->bank, 'trailerBatch');
        $generated = $reflectionMethod->invoke($this->bank);

        $this->assertEquals($this->trailerBatch, $generated);
        $this->assertEquals(240, strlen((string) $generated));
    }

    public function testTrailer()
    {
        $exclusiveTrailer = '23799999         000001000004000000                                                                                                                                                                                                             ';
        $reflectionMethod = new ReflectionMethod($this->bank, 'trailer');
        $generated = $reflectionMethod->invoke($this->bank);

        $this->assertEquals($exclusiveTrailer, $generated);
        $this->assertEquals(240, strlen((string) $generated));
    }

    public function testGenerate()
    {
        $generated = $this->bank->generate();

        $expected = $this->header .
            $this->endLine .
            $this->headerBatch .
            $this->endLine .
            $this->segmentA .
            $this->endLine .
            $this->segmentB .
            $this->endLine .
            $this->segmentA2 .
            $this->endLine .
            $this->segmentB2 .
            $this->endLine .
            $this->trailerBatch .
            $this->endLine .
            $this->trailer .
            $this->endLine;

        $this->assertEquals($expected, $generated);
        $this->assertEquals(1936, strlen((string) $generated));
    }

    public function testGetFinality()
    {
        $reflectionMethod = new ReflectionMethod($this->bank, 'getFinality');

        $this->assertEquals('0001', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_IMPOSTOS_E_TAXAS));
        $this->assertEquals('0002', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_CONCESSIONARIAS));
        $this->assertEquals('0003', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_DIVIDENDENDOS));
        $this->assertEquals('0004', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_SALARIOS));
        $this->assertEquals('0005', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_FORNECEDORES));
        $this->assertEquals('0006', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_HONORARIOS));
        $this->assertEquals('0007', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_ALUGUEIS_E_CONDOMINIOS));
        $this->assertEquals('0008', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_DUPLICATAS_E_TITULOS));
        $this->assertEquals('0009', $reflectionMethod->invoke($this->bank, Finality::PAGAMENTO_MENSALIDADE_ESCOLAR));
        $this->assertEquals('0010', $reflectionMethod->invoke($this->bank, Finality::CREDITO_CONTA));
    }

    public function testStatus()
    {
        $reflectionMethod = new ReflectionMethod($this->bank, 'getStatus');

        $this->assertEquals('17', $reflectionMethod->invoke($this->bank, Status::ALTERACAO));
        $this->assertEquals('27', $reflectionMethod->invoke($this->bank, Status::BAIXA));
        $this->assertEquals('19', $reflectionMethod->invoke($this->bank, Status::ALTERACAO_DATA));
        $this->assertEquals('00', $reflectionMethod->invoke($this->bank, Status::REGISTRO));
    }

    public function testAddPayment()
    {
        $this->assertEquals(2, count($this->bank->getPayments()));
        $this->assertNotEmpty($this->bank->getPayments());
        $this->assertContains($this->payment, $this->bank->getPayments());
    }

    public function testHeaderPosition73()
    {
        $this->bank->generate();
        $method = new ReflectionMethod($this->bank, 'header');
        $method->setAccessible(true);

        $generated = $method->invoke($this->bank);

        $this->assertEquals(' ', substr((string) $generated, 71, 1));
        $this->assertEquals('LOREM IPSUM DOLOR SIT AMET CON', substr((string) $generated, 72, 30));
        $this->assertEquals('9 LOREM', substr((string) $generated, 70, 7));
    }

    public function testHeaderBatchPosition218()
    {
        $this->bank->generate();
        $method = new ReflectionMethod($this->bank, 'headerBatch');
        $method->setAccessible(true);

        $generated = $method->invoke($this->bank);
        $this->assertEquals('12345678', substr((string) $generated, 212, 8));
        $this->assertEquals('12345678SP', substr((string) $generated, 212, 10));
    }

    public function testSegmentBPosition123()
    {
        $this->bank->generate();
        $method = new ReflectionMethod($this->bank, 'segmentB');
        $method->setAccessible(true);

        $generated = $method->invoke($this->bank, $this->payment, 1);
        $this->assertEquals('12345678', substr((string) $generated, 117, 8));
        $this->assertEquals('12345678SP', substr((string) $generated, 117, 10));
    }
}
