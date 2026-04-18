<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Strategy;

interface RoundingStrategyInterface
{
    public function round(float $fee, float $loanAmount): float;
}
