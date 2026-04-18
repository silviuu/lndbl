<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Exception;

final class InvalidTermException extends LoanFeeCalculatorException
{
    public function __construct(int $term)
    {
        parent::__construct("Invalid term: {$term}. Allowed values: 12, 24.");
    }
}
