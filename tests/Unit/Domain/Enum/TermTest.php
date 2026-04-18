<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Domain\Enum;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Exception\InvalidTermException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TermTest extends TestCase
{
    #[Test]
    public function fromInputAcceptsInt12(): void
    {
        $this->assertSame(Term::Twelve, Term::fromInput(12));
    }

    #[Test]
    public function fromInputAcceptsString24(): void
    {
        $this->assertSame(Term::TwentyFour, Term::fromInput('24'));
    }

    #[Test]
    #[DataProvider('invalidTermProvider')]
    public function fromInputRejectsInvalidValues(string|int $value): void
    {
        $this->expectException(InvalidTermException::class);
        Term::fromInput($value);
    }

    /** @return iterable<string, array{string|int}> */
    public static function invalidTermProvider(): iterable
    {
        yield 'zero' => [0];
        yield 'six' => [6];
        yield 'string 36' => ['36'];
        yield 'negative' => [-12];
    }
}
