<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Model;

final readonly class FeeBreakpoint
{
    public function __construct(
        public float $amount,
        public float $fee,
    ) {
    }
}
