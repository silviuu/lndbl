<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\Repository;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Domain\Repository\JsonFeeStructureRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JsonFeeStructureRepositoryTest extends TestCase
{
    private JsonFeeStructureRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new JsonFeeStructureRepository(
            __DIR__ . '/../../../config/fee_structure.json'
        );
    }

    #[Test]
    public function returns20BreakpointsForTerm12(): void
    {
        $this->assertCount(20, $this->repository->getBreakpointsForTerm(Term::Twelve));
    }

    #[Test]
    public function returns20BreakpointsForTerm24(): void
    {
        $this->assertCount(20, $this->repository->getBreakpointsForTerm(Term::TwentyFour));
    }

    #[Test]
    public function returnsFeeBreakpointInstances(): void
    {
        $breakpoints = $this->repository->getBreakpointsForTerm(Term::Twelve);
        $this->assertContainsOnlyInstancesOf(FeeBreakpoint::class, $breakpoints);
    }

    #[Test]
    public function breakpointsAreSortedAscendingForTerm12(): void
    {
        $breakpoints = $this->repository->getBreakpointsForTerm(Term::Twelve);
        for ($i = 1; $i < count($breakpoints); $i++) {
            $this->assertGreaterThan($breakpoints[$i - 1]->amount->cents(), $breakpoints[$i]->amount->cents());
        }
    }

    #[Test]
    public function breakpointsAreSortedAscendingForTerm24(): void
    {
        $breakpoints = $this->repository->getBreakpointsForTerm(Term::TwentyFour);
        for ($i = 1; $i < count($breakpoints); $i++) {
            $this->assertGreaterThan($breakpoints[$i - 1]->amount->cents(), $breakpoints[$i]->amount->cents());
        }
    }

    #[Test]
    public function term12BoundaryValues(): void
    {
        $breakpoints = $this->repository->getBreakpointsForTerm(Term::Twelve);
        $this->assertSame(1000.0, $breakpoints[0]->amount->toFloat());
        $this->assertSame(50.0, $breakpoints[0]->fee->toFloat());
        $last = $breakpoints[array_key_last($breakpoints)];
        $this->assertSame(20000.0, $last->amount->toFloat());
        $this->assertSame(400.0, $last->fee->toFloat());
    }

    #[Test]
    public function term24BoundaryValues(): void
    {
        $breakpoints = $this->repository->getBreakpointsForTerm(Term::TwentyFour);
        $this->assertSame(1000.0, $breakpoints[0]->amount->toFloat());
        $this->assertSame(70.0, $breakpoints[0]->fee->toFloat());
        $last = $breakpoints[array_key_last($breakpoints)];
        $this->assertSame(20000.0, $last->amount->toFloat());
        $this->assertSame(800.0, $last->fee->toFloat());
    }

    #[Test]
    public function allFeesArePositive(): void
    {
        foreach (Term::cases() as $term) {
            foreach ($this->repository->getBreakpointsForTerm($term) as $bp) {
                $this->assertGreaterThan(0, $bp->fee->toFloat(), "Fee at amount {$bp->amount->toFloat()} must be positive");
            }
        }
    }

    #[Test]
    public function throwsWhenFileNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        new JsonFeeStructureRepository('/nonexistent/path/fee_structure.json');
    }
}
