<?php

declare(strict_types=1);

namespace App\Domain\Quote\Service;

use App\Domain\Provider\Provider;
use App\Domain\Quote\Quote;

interface QuoteCalculatorInterface
{
    /** @param string[] $consideredTopics */
    public function calculate(
        Provider $provider,
        array $consideredTopics,
        float $baseQuoteValue
    ): Quote;
}
