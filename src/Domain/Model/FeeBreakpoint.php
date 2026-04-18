<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Model;

use LoanFeeCalculator\Domain\ValueObject\Money;

final readonly class FeeBreakpoint
{
    public function __construct(
        public Money $amount,
        public Money $fee,
    ) {
    }
}
