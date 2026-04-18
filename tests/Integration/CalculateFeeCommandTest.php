<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CalculateFeeCommandTest extends TestCase
{
    private static string $binPath;

    public static function setUpBeforeClass(): void
    {
        $path = realpath(__DIR__ . '/../../bin/calculate-fee');
        self::assertNotFalse($path, 'bin/calculate-fee not found');
        self::$binPath = $path;
    }

    #[Test]
    #[DataProvider('validInputProvider')]
    public function producesCorrectOutput(string $amount, string $term, string $expectedOutput): void
    {
        [$stdout, $stderr, $exitCode] = $this->execute($amount, $term);

        $this->assertSame(0, $exitCode, "stderr: {$stderr}");
        $this->assertSame($expectedOutput, trim($stdout));
    }

    /** @return iterable<string, array{string, string, string}> */
    public static function validInputProvider(): iterable
    {
        yield 'spec example 1' => ['11,500.00', '24', '460.00'];
        yield 'spec example 2' => ['19,250.00', '12', '385.00'];
        yield 'plain integer' => ['5000', '12', '100.00'];
        yield 'minimum' => ['1000', '24', '70.00'];
        yield 'maximum' => ['20000', '24', '800.00'];
    }

    #[Test]
    public function rejectsInvalidAmount(): void
    {
        [$stdout, $stderr, $exitCode] = $this->execute('500', '12');

        $this->assertSame(1, $exitCode);
        $this->assertNotEmpty($stderr);
    }

    #[Test]
    public function rejectsInvalidTerm(): void
    {
        [$stdout, $stderr, $exitCode] = $this->execute('5000', '36');

        $this->assertSame(1, $exitCode);
        $this->assertNotEmpty($stderr);
    }

    #[Test]
    public function rejectsMissingArguments(): void
    {
        [$stdout, $stderr, $exitCode] = $this->executeRaw([]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Usage:', $stderr);
    }

    #[Test]
    public function helpFlagOutputsHelpText(): void
    {
        [$stdout, $stderr, $exitCode] = $this->executeRaw(['--help']);

        $this->assertSame(0, $exitCode, "stderr: {$stderr}");
        $this->assertStringContainsString('Usage:', $stdout);
        $this->assertStringContainsString('Arguments:', $stdout);
    }

    #[Test]
    public function versionFlagOutputsVersion(): void
    {
        [$stdout, $stderr, $exitCode] = $this->executeRaw(['--version']);

        $this->assertSame(0, $exitCode, "stderr: {$stderr}");
        $this->assertStringContainsString('loan-fee-calculator', $stdout);
    }

    /** @return array{string, string, int} [stdout, stderr, exitCode] */
    private function execute(string $amount, string $term): array
    {
        return $this->executeRaw([$amount, $term]);
    }

    /** @param string[] $args
     *  @return array{string, string, int} [stdout, stderr, exitCode]
     */
    private function executeRaw(array $args): array
    {
        $cmd = 'php ' . escapeshellarg(self::$binPath);
        foreach ($args as $arg) {
            $cmd .= ' ' . escapeshellarg($arg);
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes);
        $this->assertIsResource($process);

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [(string) $stdout, (string) $stderr, $exitCode];
    }
}
