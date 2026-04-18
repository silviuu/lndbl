<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Strategy;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;

interface InterpolationStrategyInterface
{
    public function interpolate(float $amount, FeeBreakpoint $lower, FeeBreakpoint $upper): float;
}
