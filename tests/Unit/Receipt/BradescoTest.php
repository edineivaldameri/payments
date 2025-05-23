<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Tests\Unit\Receipt;

use EdineiValdameri\Payments\Enum\Bank;
use EdineiValdameri\Payments\Enum\Finality;
use EdineiValdameri\Payments\Enum\Status;
use EdineiValdameri\Payments\Interface\Address;
use EdineiValdameri\Payments\Interface\BankAccount;
use EdineiValdameri\Payments\Interface\Payer;
use EdineiValdameri\Payments\Receipt\Cnab240\Bank\Bradesco;
use EdineiValdameri\Payments\Tests\TestCase;
use Exception;

class BradescoTest extends TestCase
{
    private Payer $payer;

    private string $header = '23700000         21111111100011100091234512345679   1234550000012345679 LOREM IPSUM DOLOR SIT AMET CONBRADESCO                                10101202512595900012308901600                                                                     ';

    private string $headerBatch = '23700011C3001045 21111111100011100091234512345679   1234550000012345679 LOREM IPSUM DOLOR SIT AMET CON                                        LOREM IPSUM                   00123APTO 123       LOREM IPSUM         12345678SP01                ';

    private string $segmentA = '2370001300001A0000002371234550000012345679 LOREM IPSUM DOLOR SIT AMET CON0000000000000000123101012025BRL000000123456000000000000123456                    00000000000000000000000                                        06 0004     0BD        ';

    private string $segmentB = '2370001300002B   211111111000111LOREM IPSUM                   00123APTO 123       LOREM IPSUM    LOREM IPSUM         12345678SP01012025000000000123456000000000123456000000000123456000000000123456000000000123456123456789      0              ';

    private string $segmentA2 = '2370001300003A0000002371234550000012345679 LOREM IPSUM DOLOR SIT AMET CON0000000000000000123201012025BRL000000654321000000000000654321                    00000000000000000000000                                        06 0004     0CF        ';

    private string $segmentB2 = '2370001300004B   211111111000111LOREM IPSUM                   00123APTO 123       LOREM IPSUM    LOREM IPSUM         12345678SP01012025000000000654321000000000654321000000000654321000000000654321000000000654321123456789      0              ';

    private string $trailerBatch = '23700015         000006000000000000777777000000000000000000000000                                                                                                                                                                               ';

    private string $trailer = '23799999         000001000008000000                                                                                                                                                                                                             ';

    private string $file;

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

        $this->file = $this->header . PHP_EOL .
            $this->headerBatch . PHP_EOL .
            $this->segmentA . PHP_EOL .
            $this->segmentB . PHP_EOL .
            $this->segmentA2 . PHP_EOL .
            $this->segmentB2 . PHP_EOL .
            $this->trailerBatch . PHP_EOL .
            $this->trailer;
    }

    public function testDetail()
    {
        $bradesco = new Bradesco($this->payer, $this->file);
        $bradesco->process();

        $detail = $bradesco->getPayments()->first();

        $this->assertCount(2, $bradesco->getPayments());
        $this->assertEquals(1231, $detail->getId());
        $this->assertEquals('BD', $detail->getOccurrence());
        $this->assertEquals('Inclusão Efetuada com Sucesso', $detail->getMessage());
        $this->assertEquals(Status::LIQUIDADO, $detail->getStatus());
        $this->assertEquals(0.0, $detail->getAmount());
        $this->assertEquals(0.0, $detail->getDiscountAmount());
        $this->assertEquals(0.0, $detail->getInterestAmount());
        $this->assertEquals(0.0, $detail->getFineAmount());
        $this->assertEquals(0.0, $detail->getAbatementAmount());
        $this->assertEquals(date('Y-m-d'), $detail->getDate()->format('Y-m-d'));
        $this->assertEquals(Finality::CREDITO_CONTA, $detail->getFinality());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Method not implemented.');
        $detail->getReceiver();
    }

    public function testFileNotFund()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File not found');

        (new Bradesco($this->payer, null))->process();
    }

    public function testInvalidLineLength()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid line length');
        $file = str_replace('BD', '', $this->file);

        (new Bradesco($this->payer, $file))->process();
    }

    public function testInvalidLineType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid line type');
        $file = str_replace('2370001300002B', '2370001400002B', $this->file);

        (new Bradesco($this->payer, $file))->process();
    }

    public function testInvalidSegmentType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid segment type');
        $file = str_replace('2370001300001A', '2370001300001C', $this->file);

        (new Bradesco($this->payer, $file))->process();
    }

    public function testGetOccurrences()
    {
        $bradesco = new Bradesco($this->payer, $this->file);
        $bradesco->process();

        $this->assertEquals('BD', $bradesco->getPayments()->first()->getOccurrence());
        $this->assertEquals('CF', $bradesco->getPayments()->last()->getOccurrence());
    }

    public function testReplaceEndline()
    {
        $bradesco = new Bradesco($this->payer, $this->file);
        $bradesco->process();

        $this->assertEquals('BD', $bradesco->getPayments()->first()->getOccurrence());
        $this->assertEquals('CF', $bradesco->getPayments()->last()->getOccurrence());
    }

    public function testReplaceEndline2()
    {
        $file = $this->header . "\r\n";

        $bradesco = new Bradesco($this->payer, $file);
        $bradesco->process();

        $this->assertCount(0, $bradesco->getPayments());
    }
}
