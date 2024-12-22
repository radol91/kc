<?php

declare(strict_types=1);


namespace App\Application;

use App\Domain\Provider\ProviderStoreInterface;
use App\Domain\Quote\Service\QuoteCalculatorInterface;

class ProvidersQuotesService
{
    public const int DEFAULT_CONSIDERED_TOPIC_COUNT = 3;
    private int $consideredTopicsCount;
    public function __construct(
        private readonly ProviderStoreInterface $providerStore,
        private readonly QuoteCalculatorInterface $quoteCalculator,
        int $consideredTopicsCount = self::DEFAULT_CONSIDERED_TOPIC_COUNT,
    ) {
        $this->consideredTopicsCount = $consideredTopicsCount;
    }

    /** @return ProviderQuoteDto[] */
    public function getProvidersQuotes(GetProvidersQuotesUseCase $getProvidersQuotesUseCase): array
    {
        $quotes = [];

        $consideredTopics = $this->retrieveConsideredTopics($getProvidersQuotesUseCase);

        foreach ($this->providerStore->getProviders() as $provider) {
            $matchedTopics = $provider->matchAvailableTopics($consideredTopics);
            $quote = $this->quoteCalculator->calculate(
                provider: $provider,
                consideredTopics: $consideredTopics,
                baseQuoteValue: $getProvidersQuotesUseCase->getTotalByTopics($matchedTopics),
            );

            if (!$quote->isEmpty()) {
                $quotes[] = new ProviderQuoteDto($provider, $quote);
            }
        }

        return $quotes;
    }

    /** @return string[] */
    private function retrieveConsideredTopics(GetProvidersQuotesUseCase $getProvidersQuotesUseCase): array
    {
        $allTopics = $getProvidersQuotesUseCase->topics;
        arsort($allTopics);

        return  array_keys(array_slice($allTopics, 0, $this->consideredTopicsCount));
    }
}
