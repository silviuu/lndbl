<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Unit\Cli;

use LoanFeeCalculator\Application\FeeCalculatorInterface;
use LoanFeeCalculator\Cli\CalculateFeeCommand;
use LoanFeeCalculator\Cli\FeeFormatter;
use LoanFeeCalculator\Domain\AmountParser;
use LoanFeeCalculator\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CalculateFeeCommandTest extends TestCase
{
    private FeeCalculatorInterface $calculator;
    /** @var resource */
    private mixed $stdout;
    /** @var resource */
    private mixed $stderr;

    protected function setUp(): void
    {
        $this->calculator = $this->createStub(FeeCalculatorInterface::class);
        $this->calculator->method('calculate')->willReturn(Money::fromFloat(100.0));

        $stdout = fopen('php://memory', 'r+');
        $stderr = fopen('php://memory', 'r+');
        $this->assertNotFalse($stdout, 'Failed to open stdout stream');
        $this->assertNotFalse($stderr, 'Failed to open stderr stream');
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }

    protected function tearDown(): void
    {
        fclose($this->stdout);
        fclose($this->stderr);
    }

    // --- valid decimal amounts ---

    #[Test]
    #[DataProvider('validDecimalAmountProvider')]
    public function acceptsAmountWithUpToTwoDecimalPlaces(string $amount): void
    {
        $command = $this->makeCommand();

        $exitCode = $command->run(['calculate-fee', $amount, '12']);

        $this->assertSame(0, $exitCode, $this->stderrOutput());
    }

    /** @return iterable<string, array{string}> */
    public static function validDecimalAmountProvider(): iterable
    {
        yield 'integer, no decimal point'     => ['5000'];
        yield 'one decimal place'             => ['5000.5'];
        yield 'two decimal places'            => ['5000.50'];
        yield 'exactly two decimal places'    => ['1000.99'];
        yield 'comma-formatted, two decimals' => ['5,000.25'];
        yield 'zero pence (trailing zero)'    => ['2000.00'];
    }

    // --- invalid: more than two decimal places ---

    #[Test]
    #[DataProvider('tooManyDecimalPlacesProvider')]
    public function rejectsAmountWithMoreThanTwoDecimalPlaces(string $amount): void
    {
        $command = $this->makeCommand();

        $exitCode = $command->run(['calculate-fee', $amount, '12']);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Error:', $this->stderrOutput());
    }

    /** @return iterable<string, array{string}> */
    public static function tooManyDecimalPlacesProvider(): iterable
    {
        yield 'three decimal places'        => ['1000.123'];
        yield 'four decimal places'         => ['5000.1234'];
        yield 'many decimal places'         => ['1000.99999'];
        yield 'comma-formatted, three decs' => ['5,000.125'];
    }

    // --- help and version flags ---

    #[Test]
    #[DataProvider('helpFlagProvider')]
    public function helpFlagOutputsUsageAndReturnsZero(string $flag): void
    {
        $command = $this->makeCommand();
        $exitCode = $command->run(['calculate-fee', $flag]);

        $this->assertSame(0, $exitCode);
        rewind($this->stdout);
        $output = (string) stream_get_contents($this->stdout);
        $this->assertStringContainsString('Usage:', $output);
        $this->assertStringContainsString('Arguments:', $output);
        $this->assertStringContainsString('Examples:', $output);
    }

    /** @return iterable<string, array{string}> */
    public static function helpFlagProvider(): iterable
    {
        yield '--help' => ['--help'];
        yield '-h'     => ['-h'];
    }

    #[Test]
    #[DataProvider('versionFlagProvider')]
    public function versionFlagOutputsVersionAndReturnsZero(string $flag): void
    {
        $command = $this->makeCommand();
        $exitCode = $command->run(['calculate-fee', $flag]);

        $this->assertSame(0, $exitCode);
        rewind($this->stdout);
        $output = (string) stream_get_contents($this->stdout);
        $this->assertStringContainsString('loan-fee-calculator', $output);
    }

    /** @return iterable<string, array{string}> */
    public static function versionFlagProvider(): iterable
    {
        yield '--version' => ['--version'];
        yield '-V'        => ['-V'];
    }

    // --- helpers ---

    private function makeCommand(): CalculateFeeCommand
    {
        return new CalculateFeeCommand($this->calculator, new AmountParser(), new FeeFormatter(), $this->stdout, $this->stderr);
    }

    private function stderrOutput(): string
    {
        rewind($this->stderr);
        return (string) stream_get_contents($this->stderr);
    }
}
