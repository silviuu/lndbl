<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Application;

use LoanFeeCalculator\Application\FeeCalculator;
use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Repository\InMemoryFeeStructureRepository;
use LoanFeeCalculator\Domain\Strategy\DivisibleByFiveRoundingStrategy;
use LoanFeeCalculator\Domain\Strategy\LinearInterpolationStrategy;
use LoanFeeCalculator\Domain\ValueObject\LoanApplication;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FeeCalculatorTest extends TestCase
{
    private FeeCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new FeeCalculator(
            new InMemoryFeeStructureRepository(),
            new LinearInterpolationStrategy(),
            new DivisibleByFiveRoundingStrategy(),
        );
    }

    #[Test]
    #[DataProvider('feeCalculationProvider')]
    public function calculatesCorrectFee(float $amount, Term $term, float $expectedFee): void
    {
        $application = new LoanApplication($amount, $term);
        $fee = $this->calculator->calculate($application);
        $this->assertSame($expectedFee, $fee);
    }

    /** @return iterable<string, array{float, Term, float}> */
    public static function feeCalculationProvider(): iterable
    {
        // Spec examples
        yield 'spec: 11500 / 24m' => [11500.0, Term::TwentyFour, 460.0];
        yield 'spec: 19250 / 12m' => [19250.0, Term::Twelve, 385.0];

        // Exact breakpoints
        yield 'exact: 1000 / 12m' => [1000.0, Term::Twelve, 50.0];
        yield 'exact: 1000 / 24m' => [1000.0, Term::TwentyFour, 70.0];
        yield 'exact: 2000 / 12m' => [2000.0, Term::Twelve, 90.0];
        yield 'exact: 2000 / 24m' => [2000.0, Term::TwentyFour, 100.0];
        yield 'exact: 20000 / 12m' => [20000.0, Term::Twelve, 400.0];
        yield 'exact: 20000 / 24m' => [20000.0, Term::TwentyFour, 800.0];
        yield 'exact: 5000 / 12m' => [5000.0, Term::Twelve, 100.0];

        // Interpolation between breakpoints
        // 2000->90, 3000->90 for 12m: 2500 -> 90, total=2590, 2590%5=0
        yield 'flat interpolation: 2500 / 12m' => [2500.0, Term::Twelve, 90.0];

        // 1000->70, 2000->100 for 24m: 1500 -> 85, total=1585, 1585%5=0
        yield 'midpoint: 1500 / 24m' => [1500.0, Term::TwentyFour, 85.0];

        // Near-boundary interpolation
        yield 'just above lower bound: 1000.01 / 12m' => [1000.01, Term::Twelve, 54.99];
        yield 'just below upper bound: 1999.99 / 12m' => [1999.99, Term::Twelve, 90.01];
        yield 'just above lower bound: 1000.01 / 24m' => [1000.01, Term::TwentyFour, 74.99];
        yield 'just below upper bound: 1999.99 / 24m' => [1999.99, Term::TwentyFour, 100.01];
    }
}
