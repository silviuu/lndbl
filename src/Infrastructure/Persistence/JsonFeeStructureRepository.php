<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Infrastructure\Persistence;

use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\ValueObject\Money;
use LoanFeeCalculator\Domain\Repository\FeeStructureProviderInterface;

final class JsonFeeStructureRepository implements FeeStructureProviderInterface
{
    /** @var array<string, array<string, float>> */
    private readonly array $data;

    public function __construct(string $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new \RuntimeException("Cannot read fee structure file: {$path}");
        }

        $json = file_get_contents($path);

        /** @var array<string, array<string, float>> $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->data = $decoded;
    }

    /** @return FeeBreakpoint[] */
    public function getBreakpointsForTerm(Term $term): array
    {
        $key = (string) $term->value;

        if (!isset($this->data[$key])) {
            throw new \RuntimeException("No fee structure for term: {$term->value}");
        }

        $breakpoints = [];
        foreach ($this->data[$key] as $amount => $fee) {
            $breakpoints[] = new FeeBreakpoint(
                Money::fromFloat((float) $amount),
                Money::fromFloat((float) $fee),
            );
        }

        usort($breakpoints, static fn(FeeBreakpoint $a, FeeBreakpoint $b) => $a->amount->isGreaterThan($b->amount) ? 1 : -1);

        return $breakpoints;
    }
}
