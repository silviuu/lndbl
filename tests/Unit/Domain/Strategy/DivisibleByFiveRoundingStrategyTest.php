<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\Strategy;

use LoanFeeCalculator\Infrastructure\Strategy\DivisibleByFiveRoundingStrategy;
use LoanFeeCalculator\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DivisibleByFiveRoundingStrategyTest extends TestCase
{
    private DivisibleByFiveRoundingStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new DivisibleByFiveRoundingStrategy();
    }

    #[Test]
    #[DataProvider('roundingProvider')]
    public function roundsCorrectly(float $fee, float $loanAmount, float $expected): void
    {
        $result = $this->strategy->round(Money::fromFloat($fee), Money::fromFloat($loanAmount));
        $this->assertSame($expected, $result->toFloat());
    }

    /** @return iterable<string, array{float, float, float}> */
    public static function roundingProvider(): iterable
    {
        // 11500 + 460 = 11960, 11960 % 5 = 0, no adjustment
        yield 'already divisible' => [460.0, 11500.0, 460.0];

        // 19250 + 380 = 19630, 19630 % 5 = 0, no adjustment
        yield 'spec example 2 interpolated' => [380.0, 19250.0, 380.0];

        // 2750 + 90 = 2840, 2840 % 5 = 0, no adjustment
        yield 'no remainder' => [90.0, 2750.0, 90.0];

        // 1234 + 56 = 1290, 1290 % 5 = 0, no adjustment
        yield 'exact five total' => [56.0, 1234.0, 56.0];

        // 1001 + 50 = 1051, 1051 % 5 = 1, fee += 4 => 54.0
        yield 'needs rounding up by 4' => [50.0, 1001.0, 54.0];

        // 1002 + 50 = 1052, 1052 % 5 = 2, fee += 3 => 53.0
        yield 'needs rounding up by 3' => [50.0, 1002.0, 53.0];

        // 1003 + 50 = 1053, 1053 % 5 = 3, fee += 2 => 52.0
        yield 'needs rounding up by 2' => [50.0, 1003.0, 52.0];

        // 1004 + 50 = 1054, 1054 % 5 = 4, fee += 1 => 51.0
        yield 'needs rounding up by 1' => [50.0, 1004.0, 51.0];
    }
}
