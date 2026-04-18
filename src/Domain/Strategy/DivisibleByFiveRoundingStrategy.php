<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Strategy;

final class DivisibleByFiveRoundingStrategy implements RoundingStrategyInterface
{
    public function round(float $fee, float $loanAmount): float
    {
        $total = $loanAmount + $fee;
        $remainder = fmod($total, 5);

        if ($remainder > 0) {
            $fee += (5 - $remainder);
        }

        return round($fee, 2);
    }
}
