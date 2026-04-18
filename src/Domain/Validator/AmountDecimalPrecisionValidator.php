<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Validator;

use LoanFeeCalculator\Domain\Exception\AmountDecimalPrecisionException;

final class AmountDecimalPrecisionValidator
{
    public function validate(string $input, string $cleaned): void
    {
        $parts = explode('.', $cleaned);

        if (isset($parts[1]) && strlen($parts[1]) > 2) {
            throw new AmountDecimalPrecisionException($input);
        }
    }
}
