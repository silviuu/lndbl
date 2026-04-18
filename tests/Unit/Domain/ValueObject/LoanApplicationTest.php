<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\ValueObject;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Exception\InvalidLoanAmountException;
use LoanFeeCalculator\Domain\ValueObject\LoanApplication;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoanApplicationTest extends TestCase
{
    #[Test]
    public function createsWithValidAmount(): void
    {
        $app = new LoanApplication(5000.0, Term::Twelve);

        $this->assertSame(5000.0, $app->amount);
        $this->assertSame(Term::Twelve, $app->term);
    }

    #[Test]
    public function acceptsMinBoundary(): void
    {
        $app = new LoanApplication(1000.0, Term::TwentyFour);
        $this->assertSame(1000.0, $app->amount);
    }

    #[Test]
    public function acceptsMaxBoundary(): void
    {
        $app = new LoanApplication(20000.0, Term::Twelve);
        $this->assertSame(20000.0, $app->amount);
    }

    #[Test]
    public function rejectsOutOfRangeAmount(): void
    {
        $this->expectException(InvalidLoanAmountException::class);
        new LoanApplication(500.0, Term::Twelve);
    }
}
