<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Tests\Unit;

use EdineiValdameri\Payments\Enum\Bank;
use EdineiValdameri\Payments\Helper\Useful;
use EdineiValdameri\Payments\Tests\TestCase;
use EdineiValdameri\Payments\ValueObject\Field;
use EdineiValdameri\Payments\ValueObject\Header;
use Exception;

class UsefulTest extends TestCase
{
    public function testAddField()
    {
        $field = new Field(1, 10, '    123456');
        $this->assertEquals('    123456', $field->getValue());
    }

    public function testAddFieldException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('String $value maior que o tamanho definido em $initial e $final: $value=10 e tamanho é de: 9');
        new Field(1, 9, '1234567890');
    }

    public function testAddFieldInitialSuperiorFinalException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$initial é maior que o $final');
        new Field(10, 1, '1234567890');
    }

    public function testAddFieldSuperiorFileExceptionMutation()
    {
        $field = new Field(238, 240, '12');
        $this->assertEquals(' 12', $field->getValue());
    }

    public function testAddFieldSuperiorFileException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$initial ou $final ultrapassam o limite máximo de 240');
        new Field(239, 240, '1234567890');
    }

    public function testAddFieldSuperiorFileException2()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$initial ou $final ultrapassam o limite máximo de 240');
        new Field(238, 241, '1234567890');
    }

    public function testFormatCnab()
    {
        $this->assertEquals('0000001234', Useful::formatCnab('9', '1234', 10));
        $this->assertEquals('0000001234', Useful::formatCnab('9L', '1.2.3.4', 10));
        $this->assertEquals('0000001234', Useful::formatCnab('9L', 'AAAAAA1234', 10));
        $this->assertEquals('0000001234', Useful::formatCnab('NL', 'AAAAAA1234', 10));
        $this->assertEquals('0000123400', Useful::formatCnab('9', '1234', 10, 2));
        $this->assertEquals('0000012340', Useful::formatCnab('9', '1234', 10, 1));
        $this->assertEquals('0000000100', Useful::formatCnab('N', '1.00234', 10, 2));
        $this->assertEquals('0000000100', Useful::formatCnab('N', '1,00234', 10, 2));
        $this->assertEquals('0000000000', Useful::formatCnab('9', 'AB02,34', 10, 2));
        $this->assertEquals('0000100234', Useful::formatCnab('9', '1002.34', 10, 2));
        $this->assertEquals('ABC       ', Useful::formatCnab('X', 'ABC', 10));
        $this->assertEquals('ABC       ', Useful::formatCnab('A', 'ABC', 10));
    }

    public function testFormatCnabTypeInvalid()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tipo inválido');
        Useful::formatCnab('J', '123', 10);
    }

    public function testFormatCnabDecimalsLessThanZero()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Decimais não pode ser menor que 0');
        Useful::formatCnab('N', '100234', 10, -1);
    }

    public function testModulos()
    {
        $this->assertEquals(7, Useful::modulo11('123456789'));
        $this->assertEquals(4, Useful::modulo11('123456789', 2, 9, 1));
        $this->assertEquals(6, Useful::modulo11('123', 2, 9, 0, -1));
        $this->assertEquals(7, Useful::modulo11('123456789', 2, 9, 0, 1));
        $this->assertEquals(5, Useful::modulo11('123', 4, 10, 0, 1));
        $this->assertEquals(2, Useful::modulo11('123456789', 4, 10, 0, 1));
        $this->assertEquals(2, Useful::modulo11('123456', 4, 10, 0, -1));
        $this->assertEquals('P', Useful::modulo11('1', 1, 1, 0, 'P'));
        $this->assertEquals(-1, Useful::modulo11('1', 1, 1, 0, -1));
        $this->assertEquals(1, Useful::modulo11('1', 1, 1, 0, 1));
        $this->assertEquals(1, Useful::modulo11('1', 1, 1, 1, -1));
        $this->assertEquals(1, Useful::modulo11('1', 1, 1, 1, 1));
    }

    public function testnormalizeChars()
    {
        $this->assertEquals('AAAAAAAECEEEEIIIIEthNOOOOOUUUY', Useful::normalizeChars('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÜÚÝ'));
    }

    public function testUpper()
    {
        $this->assertEquals('ABC', Useful::upper('abc'));
        $this->assertEquals('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞŘ', Useful::upper('àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ'));
    }

    public function testKsortAbstractPart()
    {
        $header = new Header();
        $header->addField(new Field(8, 8, '1'));
        $header->addField(new Field(4, 7, '2345'));
        $header->addField(new Field(1, 3, Bank::BRADESCO->value));

        $this->assertEquals('23723451', $header->generate());
    }

    public function testFieldAlreadyExists()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Field already exists');
        $header = new Header();
        $header->addField(new Field(1, 3, Bank::BRADESCO->value));
        $header->addField(new Field(1, 3, Bank::BRADESCO->value));
    }

    public function testConflictWithField()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Conflict with field');
        $header = new Header();
        $header->addField(new Field(1, 3, Bank::BRADESCO->value));
        $header->addField(new Field(5, 7, Bank::BRADESCO->value));
        $header->addField(new Field(3, 5, Bank::BRADESCO->value));
    }

    public function testPartNotField()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not found fields');
        $header = new Header();
        $header->generate();
    }

    public function testStringWithMultipleLines()
    {
        $input = 'linha1' . PHP_EOL . 'linha2';
        $expected = ['linha1', 'linha2'];

        $this->assertSame($expected, Useful::fileToArray($input));
    }

    public function testStringWithTrailingNewLine()
    {
        $input = 'linha1' . PHP_EOL . 'linha2' . PHP_EOL;
        $expected = ['linha1', 'linha2'];

        $this->assertSame($expected, Useful::fileToArray($input));
    }

    public function testStringWithoutNewLine()
    {
        $input = 'linha_unica';

        $this->assertSame([], Useful::fileToArray($input));
    }

    public function testEmptyString()
    {
        $input = '';

        $this->assertSame([], Useful::fileToArray($input));
    }

    public function testWithInvalidTypes()
    {
        $this->assertSame([], Useful::fileToArray(['linha1', 'linha2']));
        $this->assertSame([], Useful::fileToArray(123));
        $this->assertSame([], Useful::fileToArray(null));
        $this->assertSame([], Useful::fileToArray((object) ['linha1']));
    }
}
