<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Repository;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Model\FeeBreakpoint;
use LoanFeeCalculator\Provider\FeeStructureProviderInterface;

final class JsonFeeStructureRepository implements FeeStructureProviderInterface
{
    /** @var array<string, array<string, float>> */
    private readonly array $data;

    public function __construct(string $path)
    {
        $json = @file_get_contents($path);

        if ($json === false) {
            throw new \RuntimeException("Cannot read fee structure file: {$path}");
        }

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
            $breakpoints[] = new FeeBreakpoint((float) $amount, $fee);
        }

        return $breakpoints;
    }
}
