<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\Validator;

use LoanFeeCalculator\Domain\Exception\AmountDecimalPrecisionException;
use LoanFeeCalculator\Domain\Validator\AmountDecimalPrecisionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AmountDecimalPrecisionValidatorTest extends TestCase
{
    private AmountDecimalPrecisionValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new AmountDecimalPrecisionValidator();
    }

    // --- valid inputs ---

    #[Test]
    #[DataProvider('validPrecisionProvider')]
    public function passesForAtMostTwoDecimalPlaces(string $cleaned): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validate($cleaned, $cleaned);
    }

    /** @return iterable<string, array{string}> */
    public static function validPrecisionProvider(): iterable
    {
        yield 'no decimal point'   => ['5000'];
        yield 'one decimal place'  => ['5000.5'];
        yield 'two decimal places' => ['1000.99'];
        yield 'trailing zeros'     => ['2000.00'];
    }

    // --- invalid inputs ---

    #[Test]
    #[DataProvider('tooManyDecimalsProvider')]
    public function throwsForMoreThanTwoDecimalPlaces(string $input, string $cleaned): void
    {
        $this->expectException(AmountDecimalPrecisionException::class);
        $this->expectExceptionMessage("Invalid amount: {$input}, must have up to two decimal places");

        $this->validator->validate($input, $cleaned);
    }

    /** @return iterable<string, array{string, string}> */
    public static function tooManyDecimalsProvider(): iterable
    {
        yield 'three decimal places' => ['1000.123', '1000.123'];
        yield 'four decimal places'  => ['5000.1234', '5000.1234'];
        yield 'many decimal places'  => ['1000.99999', '1000.99999'];
    }

    #[Test]
    public function exceptionMessageContainsOriginalInput(): void
    {
        $input = '1000.123';

        $this->expectException(AmountDecimalPrecisionException::class);
        $this->expectExceptionMessage("Invalid amount: {$input}");

        $this->validator->validate($input, $input);
    }
}
