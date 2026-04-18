<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Enum;

use LoanFeeCalculator\Domain\Exception\InvalidTermException;

enum Term: int
{
    case Twelve = 12;
    case TwentyFour = 24;

    public static function fromInput(string|int $value): self
    {
        $intValue = (int) $value;

        return self::tryFrom($intValue)
            ?? throw new InvalidTermException($intValue);
    }
}
