<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Exception;

final class NonNumericAmountException extends LoanFeeCalculatorException
{
    public function __construct(string $input)
    {
        parent::__construct("Invalid amount: {$input}, must be numeric");
    }
}
