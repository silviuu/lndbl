<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Strategy;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;

final class LinearInterpolationStrategy implements InterpolationStrategyInterface
{
    public function interpolate(float $amount, FeeBreakpoint $lower, FeeBreakpoint $upper): float
    {
        if ($lower->amount === $upper->amount) {
            //no interpolation needed, return lower fee
            return $lower->fee;
        }

        //calculate ratio
        $ratio = ($amount - $lower->amount) / ($upper->amount - $lower->amount);

        //use ratio to calculate fee
        return $lower->fee + $ratio * ($upper->fee - $lower->fee);
    }
}
