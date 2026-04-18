<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Application;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Domain\Strategy\InterpolationStrategyInterface;
use LoanFeeCalculator\Domain\Strategy\RoundingStrategyInterface;
use LoanFeeCalculator\Domain\ValueObject\LoanApplication;
use LoanFeeCalculator\Domain\ValueObject\Money;
use LoanFeeCalculator\Provider\FeeStructureProviderInterface;

final readonly class FeeCalculator implements FeeCalculatorInterface
{
    public function __construct(
        private FeeStructureProviderInterface $provider,
        private InterpolationStrategyInterface $interpolation,
        private RoundingStrategyInterface $rounding,
    ) {
    }

    public function calculate(LoanApplication $application): Money
    {
        $breakpoints = $this->provider->getBreakpointsForTerm($application->term);
        [$lower, $upper] = $this->findSurroundingBreakpoints($breakpoints, $application->amount);

        $fee = $this->interpolation->interpolate($application->amount, $lower, $upper);

        return $this->rounding->round($fee, $application->amount);
    }

    /**
     * @param FeeBreakpoint[] $breakpoints sorted ascending by amount
     * @return array{FeeBreakpoint, FeeBreakpoint}
     */
    private function findSurroundingBreakpoints(array $breakpoints, Money $amount): array
    {
        $lowerIndex = 0;
        $upperIndex = count($breakpoints) - 1;

        // Match for maximum and minimum amount, no interpolation needed
        if (!$amount->isGreaterThan($breakpoints[$lowerIndex]->amount)) {
            return [$breakpoints[$lowerIndex], $breakpoints[$lowerIndex]];
        }
        if (!$amount->isLessThan($breakpoints[$upperIndex]->amount)) {
            return [$breakpoints[$upperIndex], $breakpoints[$upperIndex]];
        }

        // Find the surrounding pair of breakpoints via linear scan
        foreach ($breakpoints as $key => $breakpoint) {
            if (!$breakpoint->amount->isGreaterThan($amount)) {
                $lowerIndex = $key;
            }

            if (!$breakpoint->amount->isLessThan($amount)) {
                $upperIndex = $key;
                break;
            }
        }

        return [$breakpoints[$lowerIndex], $breakpoints[$upperIndex]];
    }
}
