<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Strategy;

use LoanFeeCalculator\Domain\ValueObject\Money;

interface RoundingStrategyInterface
{
    public function round(Money $fee, Money $loanAmount): Money;
}
