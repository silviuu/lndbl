<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain;

use LoanFeeCalculator\Domain\AmountParser;
use LoanFeeCalculator\Domain\Exception\AmountDecimalPrecisionException;
use LoanFeeCalculator\Domain\Exception\NonNumericAmountException;
use LoanFeeCalculator\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AmountParserTest extends TestCase
{
    private AmountParser $parser;

    protected function setUp(): void
    {
        $this->parser = new AmountParser();
    }

    #[Test]
    #[DataProvider('validAmountProvider')]
    public function parseReturnsMoney(string $input, float $expected): void
    {
        $result = $this->parser->parse($input);
        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame($expected, $result->toFloat());
    }

    /** @return iterable<string, array{string, float}> */
    public static function validAmountProvider(): iterable
    {
        yield 'integer, no decimal point'        => ['5000', 5000.0];
        yield 'one decimal place'                => ['5000.5', 5000.5];
        yield 'two decimal places'               => ['1000.99', 1000.99];
        yield 'two decimal places, trailing zero' => ['2000.00', 2000.0];
        yield 'comma-formatted integer'          => ['5,000', 5000.0];
        yield 'comma-formatted, two decimals'    => ['11,500.25', 11500.25];
        yield 'multiple commas'                  => ['1,000,000', 1_000_000.0];
        yield 'leading whitespace'               => ['  5000', 5000.0];
        yield 'trailing whitespace'              => ['5000  ', 5000.0];
        yield 'surrounding whitespace'           => [' 11,500.25 ', 11500.25];
    }

    #[Test]
    #[DataProvider('tooManyDecimalPlacesProvider')]
    public function parseThrowsOnMoreThanTwoDecimalPlaces(string $input): void
    {
        $this->expectException(AmountDecimalPrecisionException::class);
        $this->expectExceptionMessage("Invalid amount: {$input}");

        $this->parser->parse($input);
    }

    /** @return iterable<string, array{string}> */
    public static function tooManyDecimalPlacesProvider(): iterable
    {
        yield 'three decimal places'        => ['1000.123'];
        yield 'four decimal places'         => ['5000.1234'];
        yield 'many decimal places'         => ['1000.99999'];
        yield 'comma-formatted, three decs' => ['5,000.125'];
    }

    #[Test]
    #[DataProvider('nonNumericProvider')]
    public function parseThrowsOnNonNumericInput(string $input): void
    {
        $this->expectException(NonNumericAmountException::class);
        $this->expectExceptionMessage("Invalid amount: {$input}");

        $this->parser->parse($input);
    }

    /** @return iterable<string, array{string}> */
    public static function nonNumericProvider(): iterable
    {
        yield 'letters only'    => ['abc'];
        yield 'alphanumeric'    => ['12abc'];
        yield 'empty string'    => [''];
        yield 'currency symbol' => ['£5000'];
    }
}
