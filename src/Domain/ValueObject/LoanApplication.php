<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\ValueObject;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Validator\LoanAmountRangeValidator;

final readonly class LoanApplication
{
    public function __construct(
        public float $amount,
        public Term $term,
        LoanAmountRangeValidator $validator = new LoanAmountRangeValidator(),
    ) {
        $validator->validate($amount);
    }
}
