<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\Validator;

use LoanFeeCalculator\Domain\Exception\InvalidLoanAmountException;
use LoanFeeCalculator\Domain\Validator\LoanAmountRangeValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoanAmountRangeValidatorTest extends TestCase
{
    private LoanAmountRangeValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LoanAmountRangeValidator();
    }

    // --- valid amounts ---

    #[Test]
    #[DataProvider('validAmountProvider')]
    public function passesForValidAmount(float $amount): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validate($amount);
    }

    /** @return iterable<string, array{float}> */
    public static function validAmountProvider(): iterable
    {
        yield 'minimum boundary' => [1000.0];
        yield 'maximum boundary' => [20000.0];
        yield 'mid range'        => [10000.0];
        yield 'decimal amount'   => [5000.50];
    }

    // --- invalid amounts ---

    #[Test]
    #[DataProvider('invalidAmountProvider')]
    public function throwsForOutOfRangeAmount(float $amount): void
    {
        $this->expectException(InvalidLoanAmountException::class);
        $this->validator->validate($amount);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidAmountProvider(): iterable
    {
        yield 'below min'  => [999.99];
        yield 'zero'       => [0.0];
        yield 'negative'   => [-1000.0];
        yield 'above max'  => [20000.01];
        yield 'way above'  => [50000.0];
    }
}
