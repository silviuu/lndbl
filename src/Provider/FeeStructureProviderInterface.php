<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Provider;

use LoanFeeCalculator\Domain\Enum\Term;
use LoanFeeCalculator\Domain\Model\FeeBreakpoint;

interface FeeStructureProviderInterface
{
    /** @return FeeBreakpoint[] */
    public function getBreakpointsForTerm(Term $term): array;
}
