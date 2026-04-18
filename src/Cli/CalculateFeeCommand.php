<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Cli;

use LoanFeeCalculator\Application\FeeCalculatorInterface;
use LoanFeeCalculator\Domain\AmountParser;
use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\ValueObject\LoanApplication;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class CalculateFeeCommand
{
    private const string VERSION = '1.0.0';

    /** @var resource */
    private mixed $stdout;
    /** @var resource */
    private mixed $stderr;

    /**
     * @param resource|null $stdout
     * @param resource|null $stderr
     */
    public function __construct(
        private readonly FeeCalculatorInterface $calculator,
        private readonly AmountParser $amountParser,
        private readonly FeeFormatter $feeFormatter,
        mixed $stdout = null,
        mixed $stderr = null,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
        $this->stdout = $stdout ?? STDOUT;
        $this->stderr = $stderr ?? STDERR;
    }

    /** @param string[] $argv */
    public function run(array $argv): int
    {
        if (in_array('--help', $argv, true) || in_array('-h', $argv, true)) {
            fwrite($this->stdout, $this->helpText());
            return 0;
        }

        if (in_array('--version', $argv, true) || in_array('-V', $argv, true)) {
            fwrite($this->stdout, "loan-fee-calculator " . self::VERSION . "\n");
            return 0;
        }

        if (count($argv) < 3) {
            fwrite($this->stderr, "Usage: calculate-fee <amount> <term>\n");
            fwrite($this->stderr, "Run 'calculate-fee --help' for more information.\n");
            return 1;
        }

        try {
            $amount = $this->amountParser->parse($argv[1]);
            $term = Term::fromInput($argv[2]);
            $application = new LoanApplication($amount, $term);
            $fee = $this->calculator->calculate($application);

            $this->logger->info('Fee calculated', [
                'amount' => $amount,
                'term'   => $term->value,
                'fee'    => $fee,
            ]);

            fwrite($this->stdout, $this->feeFormatter->format($fee) . "\n");

            return 0;
        } catch (\Throwable $e) {
            $this->logger->warning('Calculation failed', ['error' => $e->getMessage()]);
            fwrite($this->stderr, "Error: {$e->getMessage()}\n");
            return 1;
        }
    }

    private function helpText(): string
    {
        return <<<TEXT
        Usage: calculate-fee <amount> <term>

        Arguments:
          amount  Loan amount between 1,000 and 20,000 (up to 2 decimal places, comma-formatted accepted)
          term    Loan term in months: 12 or 24

        Options:
          -h, --help     Show this help message
          -V, --version  Show version information

        Examples:
          calculate-fee 11500 24
          calculate-fee 1,000.00 12
          calculate-fee 19,250.00 12

        TEXT;
    }
}
