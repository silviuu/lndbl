<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Validator;

use LoanFeeCalculator\Domain\Exception\InvalidLoanAmountException;
use LoanFeeCalculator\Domain\ValueObject\Money;

final class LoanAmountRangeValidator
{
    private static Money $min;
    private static Money $max;

    public function validate(Money $amount): void
    {
        self::$min ??= Money::fromFloat(1_000);
        self::$max ??= Money::fromFloat(20_000);

        if ($amount->isLessThan(self::$min) || $amount->isGreaterThan(self::$max)) {
            throw new InvalidLoanAmountException($amount->toFloat());
        }
    }
}
