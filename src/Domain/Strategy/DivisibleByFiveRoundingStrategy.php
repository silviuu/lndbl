<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Strategy;

use LoanFeeCalculator\Domain\ValueObject\Money;

final class DivisibleByFiveRoundingStrategy implements RoundingStrategyInterface
{
    private const int FIVE_POUNDS_IN_CENTS = 500;

    public function round(Money $fee, Money $loanAmount): Money
    {
        $total     = $loanAmount->add($fee);
        $remainder = $total->remainderOf(self::FIVE_POUNDS_IN_CENTS);

        if ($remainder !== 0) {
            $fee = $fee->addCents(self::FIVE_POUNDS_IN_CENTS - $remainder);
        }

        return $fee;
    }
}
