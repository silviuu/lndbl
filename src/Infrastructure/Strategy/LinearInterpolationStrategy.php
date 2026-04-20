<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Infrastructure\Strategy;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Domain\Strategy\InterpolationStrategyInterface;
use LoanFeeCalculator\Domain\ValueObject\Money;

final class LinearInterpolationStrategy implements InterpolationStrategyInterface
{
    public function interpolate(Money $amount, FeeBreakpoint $lower, FeeBreakpoint $upper): Money
    {
        if ($lower->amount->equals($upper->amount)) {
            return $lower->fee;
        }

        // All arithmetic is done in integer cents via bcmath to avoid float precision loss
        $amountCents   = (string) $amount->cents();
        $lowerCents    = (string) $lower->amount->cents();
        $upperCents    = (string) $upper->amount->cents();
        $lowerFeeCents = (string) $lower->fee->cents();
        $upperFeeCents = (string) $upper->fee->cents();

        $ratio = bcdiv(
            bcsub($amountCents, $lowerCents, 10),
            bcsub($upperCents, $lowerCents, 10),
            10,
        );

        $feeCents = bcadd(
            $lowerFeeCents,
            bcmul($ratio, bcsub($upperFeeCents, $lowerFeeCents, 10), 10),
            10,
        );

        return Money::ofCents((int) round((float) $feeCents));
    }
}
