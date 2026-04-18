<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Cli;

use LoanFeeCalculator\Cli\FeeFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FeeFormatterTest extends TestCase
{
    private FeeFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new FeeFormatter();
    }

    #[Test]
    #[DataProvider('formatProvider')]
    public function formatReturnsFormattedString(float $fee, string $expected): void
    {
        $this->assertSame($expected, $this->formatter->format($fee));
    }

    /** @return iterable<string, array{float, string}> */
    public static function formatProvider(): iterable
    {
        yield 'whole number'       => [100.0,  '100.00'];
        yield 'one decimal place'  => [100.5,  '100.50'];
        yield 'two decimal places' => [100.25, '100.25'];
        yield 'zero fee'           => [0.0,    '0.00'];
    }
}
