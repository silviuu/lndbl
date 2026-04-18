<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Application;

use LoanFeeCalculator\Domain\ValueObject\LoanApplication;

interface FeeCalculatorInterface
{
    public function calculate(LoanApplication $application): float;
}
