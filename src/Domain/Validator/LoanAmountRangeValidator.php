<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Validator;

use LoanFeeCalculator\Domain\Exception\InvalidLoanAmountException;

final class LoanAmountRangeValidator
{
    public function validate(float $amount): void
    {
        if ($amount < 1_000 || $amount > 20_000) {
            throw new InvalidLoanAmountException($amount);
        }
    }
}
