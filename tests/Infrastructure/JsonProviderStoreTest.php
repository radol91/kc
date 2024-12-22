<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure;

use App\Domain\Provider\Provider;
use App\Infrastructure\JsonProviderStore;
use PHPUnit\Framework\TestCase;

class JsonProviderStoreTest extends TestCase
{
    public function testItThrowsExceptionWhenFileNotFound(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessageMatches('/JSON file with providers not found/');

        new JsonProviderStore(__DIR__.'/../Assets/not_exists.json');
    }

    public function testItThrowsExceptionWhenFileJsonIsInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessageMatches('/JSON file is malformed/');

        new JsonProviderStore(__DIR__.'/../Assets/malformed_json.json');
    }

    public function testItThrowsExceptionWhenMissingProviderTopicsKey(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessageMatches('/Missing `provider_topics` key in JSON file/');

        new JsonProviderStore(__DIR__.'/../Assets/missing_provider_topic_key.json');
    }

    public function testItReturnsProvidersFromDefaultFile(): void
    {
        $store = new JsonProviderStore();
        $providers = $store->getProviders();
        self::assertCount(3, $providers);
        self::assertEquals(
            [
                'provider_a',
                'provider_b',
                'provider_c',
            ],
            array_map(static fn(Provider $provider) => $provider->name->value, $providers),
        );
    }

    public function testItRetrievesTopicsFromDefaultFile(): void
    {
        $store = new JsonProviderStore();
        $providers = $store->getProviders();

        self::assertCount(2, $providers[0]->matchAvailableTopics(['math', 'science']));
        self::assertCount(2, $providers[1]->matchAvailableTopics(['reading', 'science']));
        self::assertCount(2, $providers[2]->matchAvailableTopics(['history', 'math']));
    }
}
