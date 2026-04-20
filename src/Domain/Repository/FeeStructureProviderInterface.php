<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\Repository;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Model\FeeBreakpoint;

interface FeeStructureProviderInterface
{
    /** @return FeeBreakpoint[] */
    public function getBreakpointsForTerm(Term $term): array;
}
