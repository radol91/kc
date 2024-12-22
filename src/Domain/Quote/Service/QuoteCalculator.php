<?php

declare(strict_types=1);


namespace App\Domain\Quote\Service;

use App\Domain\Provider\Provider;
use App\Domain\Quote\Exception\QuoteCalculatorConditionNotHandledException;
use App\Domain\Quote\Quote;

class QuoteCalculator implements QuoteCalculatorInterface
{
    public const float DEFAULT_MULTIPLE_TOPICS_WEIGHT = 0.1;
    public const array DEFAULT_PRIORITY_WEIGHT_MAP = [
        0 => 0.2,
        1 => 0.25,
        2 => 0.3,
    ];

    private float $multipleTopicsWeight;
    private array $priorityWeightMap;


    /** @param array<int, float> $priorityWeightMap */
    public function __construct(
        float $multipleTopicsWeight = self::DEFAULT_MULTIPLE_TOPICS_WEIGHT,
        array $priorityWeightMap = self::DEFAULT_PRIORITY_WEIGHT_MAP,
    ) {
        $this->multipleTopicsWeight = $multipleTopicsWeight;

        // Normalize weights map to start from 0
        $this->priorityWeightMap = array_values($priorityWeightMap);
    }

    /**
     * {@inheritDoc}
     */
    public function calculate(
        Provider $provider,
        array $consideredTopics,
        int $baseQuoteValue
    ): Quote {
        $matchedTopics = $provider->matchAvailableTopics($consideredTopics);
        if (empty($matchedTopics)) {
            return Quote::createEmpty();
        }

        if (1 === count($matchedTopics)) {
            $matchedTopic = current($matchedTopics);
            $priority = array_search($matchedTopic, $consideredTopics);

            if (!isset($this->priorityWeightMap[$priority])) {
                throw new \RuntimeException('Priority map is not set properly.');
            }

            return new Quote($this->priorityWeightMap[$priority] * $baseQuoteValue);
        }

        if (2 === count($matchedTopics)) {
            return new Quote($this->multipleTopicsWeight * $baseQuoteValue);
        }

        throw new QuoteCalculatorConditionNotHandledException(
            'Calculating quote for available topics greater than 2 not handled yet.'
        );
    }
}
