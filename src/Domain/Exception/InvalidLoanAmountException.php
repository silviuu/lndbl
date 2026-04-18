<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Exception;

final class InvalidLoanAmountException extends LoanFeeCalculatorException
{
    public function __construct(float $amount)
    {
        parent::__construct(
            "Invalid loan amount: {$amount}. Must be between 1,000 and 20,000."
        );
    }
}
