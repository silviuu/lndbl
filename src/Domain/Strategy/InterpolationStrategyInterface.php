<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Strategy;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Domain\ValueObject\Money;

interface InterpolationStrategyInterface
{
    public function interpolate(Money $amount, FeeBreakpoint $lower, FeeBreakpoint $upper): Money;
}
