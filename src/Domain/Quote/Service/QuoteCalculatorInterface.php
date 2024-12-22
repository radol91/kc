<?php

declare(strict_types=1);

namespace App\Domain\Quote\Service;

use App\Domain\Provider\Provider;
use App\Domain\Quote\Exception\QuoteCalculatorException;
use App\Domain\Quote\Quote;

interface QuoteCalculatorInterface
{
    /**
     * @throws QuoteCalculatorException
     *
     * @param string[] $consideredTopics
     */
    public function calculate(
        Provider $provider,
        array $consideredTopics,
        int $baseQuoteValue
    ): Quote;
}
