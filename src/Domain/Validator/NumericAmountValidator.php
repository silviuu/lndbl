<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Validator;

use LoanFeeCalculator\Domain\Exception\NonNumericAmountException;

final class NumericAmountValidator
{
    public function validate(string $input, string $cleaned): void
    {
        if (!is_numeric($cleaned)) {
            throw new NonNumericAmountException($input);
        }
    }
}
