<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Application;

use LoanFeeCalculator\Domain\ValueObject\LoanApplication;
use LoanFeeCalculator\Domain\ValueObject\Money;

interface FeeCalculatorInterface
{
    public function calculate(LoanApplication $application): Money;
}
