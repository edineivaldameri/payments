<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Tests\Unit;

use EdineiValdameri\Pagamentos\Helper\CalculationDV;
use EdineiValdameri\Pagamentos\Tests\TestCase;

class CalculationDVTest extends TestCase
{
    public function testBradescoAgency()
    {
        $this->assertEquals(3, CalculationDV::bradescoAgency('1234'));
        $this->assertEquals(5, CalculationDV::bradescoAgency('12345'));
        $this->assertEquals(0, CalculationDV::bradescoAgency('2345'));
        $this->assertEquals(8, CalculationDV::bradescoAgency('3456'));
        $this->assertEquals(5, CalculationDV::bradescoAgency('4567'));
        $this->assertEquals(5, CalculationDV::bradescoAgency('7890'));
        $this->assertEquals(9, CalculationDV::bradescoAgency('709'));
        $this->assertEquals(0, CalculationDV::bradescoAgency('739'));
        $this->assertEquals(6, CalculationDV::bradescoAgency('7430'));
        $this->assertEquals(6, CalculationDV::bradescoAgency('4902'));
        $this->assertEquals(6, CalculationDV::bradescoAgency('1000'));
        $this->assertEquals(0, CalculationDV::bradescoAgency('0'));
        $this->assertEquals(0, CalculationDV::bradescoAgency(''));
    }

    public function testBradescoAccount()
    {
        $this->assertEquals(0, CalculationDV::bradescoAccount('123456'));
        $this->assertEquals(9, CalculationDV::bradescoAccount('654321'));
        $this->assertEquals(8, CalculationDV::bradescoAccount('262240'));
        $this->assertEquals(0, CalculationDV::bradescoAccount('739'));
        $this->assertEquals(0, CalculationDV::bradescoAccount('0'));
        $this->assertEquals(0, CalculationDV::bradescoAccount(''));
        $this->assertEquals(9, CalculationDV::bradescoAccount('1234567'));
    }
}
