<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\Validator;

use LoanFeeCalculator\Domain\Exception\NonNumericAmountException;
use LoanFeeCalculator\Domain\Validator\NumericAmountValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NumericAmountValidatorTest extends TestCase
{
    private NumericAmountValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new NumericAmountValidator();
    }

    // --- valid inputs ---

    #[Test]
    #[DataProvider('validNumericProvider')]
    public function passesForNumericInput(string $cleaned): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validate($cleaned, $cleaned);
    }

    /** @return iterable<string, array{string}> */
    public static function validNumericProvider(): iterable
    {
        yield 'integer'            => ['5000'];
        yield 'one decimal place'  => ['5000.5'];
        yield 'two decimal places' => ['1000.99'];
        yield 'trailing zeros'     => ['2000.00'];
        yield 'zero'               => ['0'];
        yield 'negative integer'   => ['-1000'];
    }

    // --- invalid inputs ---

    #[Test]
    #[DataProvider('nonNumericProvider')]
    public function throwsForNonNumericInput(string $input, string $cleaned): void
    {
        $this->expectException(NonNumericAmountException::class);
        $this->expectExceptionMessage("Invalid amount: {$input}, must be numeric");

        $this->validator->validate($input, $cleaned);
    }

    /** @return iterable<string, array{string, string}> */
    public static function nonNumericProvider(): iterable
    {
        yield 'letters only'      => ['abc', 'abc'];
        yield 'alphanumeric'      => ['12abc', '12abc'];
        yield 'empty string'      => ['', ''];
        yield 'currency symbol'   => ['£5000', '£5000'];
        yield 'comma not removed' => ['1,000', '1,000'];
    }

    #[Test]
    public function exceptionMessageContainsOriginalInput(): void
    {
        $input = 'not-a-number';

        $this->expectException(NonNumericAmountException::class);
        $this->expectExceptionMessage("Invalid amount: {$input}");

        $this->validator->validate($input, $input);
    }
}
