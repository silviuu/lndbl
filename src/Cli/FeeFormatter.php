<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Cli;

use LoanFeeCalculator\Domain\ValueObject\Money;

final class FeeFormatter
{
    public function format(Money $fee): string
    {
        return number_format($fee->toFloat(), 2, '.', '');
    }
}
