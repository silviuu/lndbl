<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\Strategy;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Domain\Strategy\LinearInterpolationStrategy;
use LoanFeeCalculator\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LinearInterpolationStrategyTest extends TestCase
{
    private LinearInterpolationStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new LinearInterpolationStrategy();
    }

    #[Test]
    #[DataProvider('interpolationProvider')]
    public function interpolatesCorrectly(
        float $amount,
        float $lowerAmt,
        float $lowerFee,
        float $upperAmt,
        float $upperFee,
        float $expected,
    ): void {
        $lower = new FeeBreakpoint(Money::fromFloat($lowerAmt), Money::fromFloat($lowerFee));
        $upper = new FeeBreakpoint(Money::fromFloat($upperAmt), Money::fromFloat($upperFee));

        $result = $this->strategy->interpolate(Money::fromFloat($amount), $lower, $upper);

        $this->assertEqualsWithDelta($expected, $result->toFloat(), 0.0001);
    }

    /** @return iterable<string, array{float, float, float, float, float, float}> */
    public static function interpolationProvider(): iterable
    {
        yield 'exact midpoint' => [11500, 11000, 440, 12000, 480, 460.0];
        yield 'quarter point' => [11250, 11000, 440, 12000, 480, 450.0];
        yield 'three-quarter' => [11750, 11000, 440, 12000, 480, 470.0];
        yield 'at lower bound' => [11000, 11000, 440, 12000, 480, 440.0];
        yield 'at upper bound' => [12000, 11000, 440, 12000, 480, 480.0];
        yield 'same breakpoint' => [5000, 5000, 100, 5000, 100, 100.0];
    }
}
