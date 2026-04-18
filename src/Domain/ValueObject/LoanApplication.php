<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\ValueObject;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Validator\LoanAmountRangeValidator;
use LoanFeeCalculator\Domain\ValueObject\Money;

final readonly class LoanApplication
{
    public function __construct(
        public Money $amount,
        public Term $term,
        LoanAmountRangeValidator $validator = new LoanAmountRangeValidator(),
    ) {
        $validator->validate($amount);
    }
}
