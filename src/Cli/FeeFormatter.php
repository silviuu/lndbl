<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Cli;

final class FeeFormatter
{
    public function format(float $fee): string
    {
        return number_format($fee, 2, '.', '');
    }
}
