<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Application;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Domain\Strategy\InterpolationStrategyInterface;
use LoanFeeCalculator\Domain\Strategy\RoundingStrategyInterface;
use LoanFeeCalculator\Domain\ValueObject\LoanApplication;
use LoanFeeCalculator\Provider\FeeStructureProviderInterface;

final readonly class FeeCalculator implements FeeCalculatorInterface
{
    public function __construct(
        private FeeStructureProviderInterface $provider,
        private InterpolationStrategyInterface $interpolation,
        private RoundingStrategyInterface $rounding,
    ) {
    }

    public function calculate(LoanApplication $application): float
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
    private function findSurroundingBreakpoints(array $breakpoints, float $amount): array
    {
        $low = 0;
        $high = count($breakpoints) - 1;

        // Match for maximum and minimum amount, no interpolation needed
        if ($amount <= $breakpoints[$low]->amount) {
            return [$breakpoints[$low], $breakpoints[$low]];
        }
        if ($amount >= $breakpoints[$high]->amount) {
            return [$breakpoints[$high], $breakpoints[$high]];
        }

        // find the surrounding pair of breakpoints
        foreach ($breakpoints as $key => $breakpoint) {
            if ($breakpoint->amount <= $amount) {
                $low = $key;
            }

            if ($breakpoint->amount >= $amount) {
                $high = $key;
                break;
            }
        }

        return [$breakpoints[$low], $breakpoints[$high]];
    }
}
