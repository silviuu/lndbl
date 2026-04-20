<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Model;

final readonly class BreakpointPair
{
    public function __construct(
        public FeeBreakpoint $lower,
        public FeeBreakpoint $upper,
    ) {}
}
