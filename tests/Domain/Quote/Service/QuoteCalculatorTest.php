<?php

declare(strict_types=1);

namespace App\Tests\Domain\Quote\Service;

use App\Domain\Provider\Provider;
use App\Domain\Quote\Exception\QuoteCalculatorConditionNotHandledException;
use App\Domain\Quote\Quote;
use App\Domain\Quote\Service\QuoteCalculator;
use PHPUnit\Framework\TestCase;

class QuoteCalculatorTest extends TestCase
{
    public function testItCalculatesEmptyQuote(): void
    {
        $quoteCalculator = new QuoteCalculator();

        $provider = $this->createMock(Provider::class);
        $provider->method('matchAvailableTopics')->willReturn([]);

        $result = $quoteCalculator->calculate($provider, ['topic1'], 10);
        self::assertEquals($result, Quote::createEmpty());
    }

    /** @dataProvider calculateQuoteValueProviderForSingleMatch */
    public function testItCalculatesQuoteForSingleMatchedTopicAndDefaultPriorityMap(
        int $baseQuoteValue,
        array $consideredTopics,
        float $expectedQuoteValue,
    ): void {
        $quoteCalculator = new QuoteCalculator();

        $provider = $this->createMock(Provider::class);
        $provider->method('matchAvailableTopics')->willReturn(['matched']);

        $result = $quoteCalculator->calculate($provider, $consideredTopics, $baseQuoteValue);
        self::assertEquals($expectedQuoteValue, $result->value);
    }

    public static function calculateQuoteValueProviderForSingleMatch(): \Generator
    {
        yield '1st priority' => [100, ['matched', 'foo', 'bar'], 20.0];
        yield '2nd priority' => [100, ['foo', 'matched', 'bar'], 25.0];
        yield '3rd priority' => [100, ['foo', 'bar', 'matched'], 30.0];
    }

    /** @dataProvider calculateQuoteValueProviderForTwoMatches */
    public function testItCalculatesQuoteForTwoMatchedTopicsAndDefaultPriorityMap(
        int $baseQuoteValue,
        array $consideredTopics,
        float $expectedQuoteValue,
    ): void {
        $quoteCalculator = new QuoteCalculator();

        $provider = $this->createMock(Provider::class);
        $provider->method('matchAvailableTopics')->willReturn(['matched1', 'matched2']);

        $result = $quoteCalculator->calculate($provider, $consideredTopics, $baseQuoteValue);
        self::assertEquals($expectedQuoteValue, $result->value);
    }

    public static function calculateQuoteValueProviderForTwoMatches(): \Generator
    {
        yield '1st priority' => [100, ['matched1', 'matched2', 'bar'], 10.0];
        yield '2nd priority' => [100, ['foo', 'matched1', 'matched2'], 10.0];
        yield '3rd priority' => [100, ['matched2', 'bar', 'matched1'], 10.0];
    }

    public function testItThrowsExceptionsWhenMoreThanTwoTopicsMatched(): void {
        $quoteCalculator = new QuoteCalculator();

        $provider = $this->createMock(Provider::class);
        $provider->method('matchAvailableTopics')->willReturn(['matched1', 'matched2', 'matched3']);

        $this->expectException(QuoteCalculatorConditionNotHandledException::class);

        $quoteCalculator->calculate($provider, [], 100);
    }

    /** @dataProvider calculateQuoteValueProviderForSingleMatchForCustomPriorityMap */
    public function testItCalculatesQuoteForSingleMatchedTopicAndCustomPriorityMap(
        int $baseQuoteValue,
        array $consideredTopics,
        float $expectedQuoteValue,
    ): void {
        $quoteCalculator = new QuoteCalculator(
            priorityWeightMap: [
                0 => 0.1,
                1 => 0.15,
                2 => 0.20,
            ]
        );

        $provider = $this->createMock(Provider::class);
        $provider->method('matchAvailableTopics')->willReturn(['matched']);

        $result = $quoteCalculator->calculate($provider, $consideredTopics, $baseQuoteValue);
        self::assertEquals($expectedQuoteValue, $result->value);
    }

    public static function calculateQuoteValueProviderForSingleMatchForCustomPriorityMap(): \Generator
    {
        yield '1st priority' => [100, ['matched', 'foo', 'bar'], 10.0];
        yield '2nd priority' => [100, ['foo', 'matched', 'bar'], 15.0];
        yield '3rd priority' => [100, ['foo', 'bar', 'matched'], 20.0];
    }

    public function testItCalculatesQuoteForTwoTopicsMatchedTopicAndCustomMultipleTopicsWeight(): void
    {
        $quoteCalculator = new QuoteCalculator(
            multipleTopicsWeight: 0.2
        );

        $provider = $this->createMock(Provider::class);
        $provider->method('matchAvailableTopics')->willReturn(['matched1', 'matched2']);

        $result = $quoteCalculator->calculate($provider, [], 100);
        self::assertEquals(20.0, $result->value);
    }

    public function testItThrowsExceptionWhenPriorityNotFoundInMap(): void
    {
        $quoteCalculator = new QuoteCalculator(
            priorityWeightMap: [
                0 => 0.1,
                1 => 0.15,
            ]
        );

        $provider = $this->createMock(Provider::class);
        $provider->method('matchAvailableTopics')->willReturn(['matched1']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Priority map is not set properly/');

        $quoteCalculator->calculate($provider, ['foo', 'bar', 'matched1'], 100);
    }
}
