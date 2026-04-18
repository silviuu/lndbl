<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\ValueObject;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Exception\InvalidLoanAmountException;
use LoanFeeCalculator\Domain\ValueObject\LoanApplication;
use LoanFeeCalculator\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoanApplicationTest extends TestCase
{
    #[Test]
    public function createsWithValidAmount(): void
    {
        $app = new LoanApplication(Money::fromFloat(5000.0), Term::Twelve);

        $this->assertSame(5000.0, $app->amount->toFloat());
        $this->assertSame(Term::Twelve, $app->term);
    }

    #[Test]
    public function acceptsMinBoundary(): void
    {
        $app = new LoanApplication(Money::fromFloat(1000.0), Term::TwentyFour);
        $this->assertSame(1000.0, $app->amount->toFloat());
    }

    #[Test]
    public function acceptsMaxBoundary(): void
    {
        $app = new LoanApplication(Money::fromFloat(20000.0), Term::Twelve);
        $this->assertSame(20000.0, $app->amount->toFloat());
    }

    #[Test]
    public function rejectsOutOfRangeAmount(): void
    {
        $this->expectException(InvalidLoanAmountException::class);
        new LoanApplication(Money::fromFloat(500.0), Term::Twelve);
    }
}
