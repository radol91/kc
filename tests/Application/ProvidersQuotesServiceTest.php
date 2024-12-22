<?php

declare(strict_types=1);


namespace App\Tests\Application;

use App\Application\GetProvidersQuotesUseCase;
use App\Application\ProviderQuoteDto;
use App\Application\ProvidersQuotesService;
use App\Domain\Provider\Provider;
use App\Domain\Provider\ProviderName;
use App\Domain\Provider\ProviderStoreInterface;
use App\Domain\Quote\Quote;
use App\Domain\Quote\Service\QuoteCalculatorInterface;
use PHPUnit\Framework\TestCase;

class ProvidersQuotesServiceTest extends TestCase
{
    public function testItGetProvidersFromStore(): void
    {
        $providersStoreMock = $this->createMock(ProviderStoreInterface::class);
        $providersStoreMock
            ->expects($this->once())
            ->method('getProviders')
            ->willReturn([])
        ;

        $service = new ProvidersQuotesService(
            $providersStoreMock,
            $this->createMock(QuoteCalculatorInterface::class)
        );
        $service->getProvidersQuotes(new GetProvidersQuotesUseCase([]));
    }

    public function testItSlicesTopicsToDefaultConsideredTopicsCountParameter(): void
    {
        $providersStoreMock = $this->createMock(ProviderStoreInterface::class);
        $providersStoreMock
            ->method('getProviders')
            ->willReturn([$providerMock = $this->createMock(Provider::class)])
        ;

        $allTopics = [
            'topic1' => 1,
            'topic2' => 2,
            'topic3' => 3,
            'topic4' => 4,
        ];

        $expectedConsideredTopics = [
            'topic4',
            'topic3',
            'topic2',
        ];

        $providerMock
            ->expects($this->once())
            ->method('matchAvailableTopics')
            ->with(self::callback(static function ($executedWithConsideredTopics) use ($expectedConsideredTopics) {
                self::assertCount(
                    3,
                    $executedWithConsideredTopics,
                    'Topics by default should be sliced to 3 elements with max values.'
                );
                self::assertEmpty(
                    array_diff($executedWithConsideredTopics, $expectedConsideredTopics),
                    'Topics by default should be sliced to 3 elements with max values.'
                );

                return true;
            }))
            ->willReturn([])
        ;

        $service = new ProvidersQuotesService(
            $providersStoreMock,
            $this->createMock(QuoteCalculatorInterface::class)
        );
        $service->getProvidersQuotes(new GetProvidersQuotesUseCase($allTopics));
    }

    public function testItSlicesTopicsToCustomConsideredTopicsCountParameter(): void
    {
        $providersStoreMock = $this->createMock(ProviderStoreInterface::class);
        $providersStoreMock
            ->method('getProviders')
            ->willReturn([$providerMock = $this->createMock(Provider::class)])
        ;

        $allTopics = [
            'topic1' => 1,
            'topic2' => 2,
            'topic3' => 3,
            'topic4' => 4,
        ];

        $expectedConsideredTopics = [
            'topic4',
            'topic3',
        ];

        $customConsideredTopicsCount = 2;
        $providerMock
            ->expects($this->once())
            ->method('matchAvailableTopics')
            ->with(self::callback(static function ($executedWithConsideredTopics) use ($customConsideredTopicsCount, $expectedConsideredTopics) {
                self::assertCount(
                    $customConsideredTopicsCount,
                    $executedWithConsideredTopics,
                    "Topics from UseCase should be sliced to {$customConsideredTopicsCount} elements with max values."
                );
                self::assertEmpty(
                    array_diff($executedWithConsideredTopics, $expectedConsideredTopics),
                    "Topics from UseCase should be sliced to {$customConsideredTopicsCount} elements with max values."
                );

                return true;
            }))
            ->willReturn([])
        ;

        $service = new ProvidersQuotesService(
            providerStore: $providersStoreMock,
            quoteCalculator: $this->createMock(QuoteCalculatorInterface::class),
            consideredTopicsCount: $customConsideredTopicsCount,
        );
        $service->getProvidersQuotes(new GetProvidersQuotesUseCase($allTopics));
    }

    public function testItReturnsEmptyQuotesWhenQuoteIsEmpty(): void
    {
        $providersStoreMock = $this->createMock(ProviderStoreInterface::class);
        $providersStoreMock
            ->method('getProviders')
            ->willReturn([$this->createMock(Provider::class)])
        ;
        $calculatorMock = $this->createMock(QuoteCalculatorInterface::class);
        $calculatorMock
            ->expects($this->once())
            ->method('calculate')
            ->willReturn(Quote::createEmpty())
        ;

        $service = new ProvidersQuotesService(
            $providersStoreMock,
            $calculatorMock,
        );
        $result = $service->getProvidersQuotes(new GetProvidersQuotesUseCase([]));

        self::assertEmpty($result);
    }

    public function testItReturnsQuotes(): void
    {
        $providersStoreMock = $this->createMock(ProviderStoreInterface::class);
        $providersStoreMock
            ->method('getProviders')
            ->willReturn([$provider = new Provider('id', new ProviderName('name'))])
        ;

        $calculatorMock = $this->createMock(QuoteCalculatorInterface::class);
        $calculatorMock
            ->method('calculate')
            ->willReturn($quote = new Quote(10.0))
        ;

        $service = new ProvidersQuotesService(
            $providersStoreMock,
            $calculatorMock,
        );
        $result = $service->getProvidersQuotes(new GetProvidersQuotesUseCase([]));

        self::assertNotEmpty($result);
        self::assertCount(1, $result);
        self::assertContainsEquals(new ProviderQuoteDto($provider, $quote), $result);
    }
}
