<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain;

use LoanFeeCalculator\Domain\Validator\AmountDecimalPrecisionValidator;
use LoanFeeCalculator\Domain\Validator\NumericAmountValidator;
use LoanFeeCalculator\Domain\ValueObject\Money;

final class AmountParser
{
    public function __construct(
        private readonly NumericAmountValidator $numericAmountValidator = new NumericAmountValidator(),
        private readonly AmountDecimalPrecisionValidator $decimalPrecisionValidator = new AmountDecimalPrecisionValidator(),
    ) {
    }

    public function parse(string $input): Money
    {
        $cleaned = str_replace(',', '', trim($input));

        $this->numericAmountValidator->validate($input, $cleaned);
        $this->decimalPrecisionValidator->validate($input, $cleaned);

        return Money::fromFloat((float) $cleaned);
    }
}
