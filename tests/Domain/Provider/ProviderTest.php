<?php

declare(strict_types=1);


namespace App\Tests\Domain\Provider;

use App\Domain\Provider\Provider;
use App\Domain\Provider\ProviderName;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    public function testItIsConstructed(): void
    {
        $provider = new Provider('id', new ProviderName('name'));

        self::assertEquals('id', $provider->id);
        self::assertEquals('name', $provider->name->value);
    }

    public function testItMatchesAvailableTopics(): void
    {
        $provider = new Provider('id', new ProviderName('name'));
        $provider->addTopic($topic = 'newTopic');

        self::assertNotEmpty($provider->matchAvailableTopics([$topic]));
        self::assertCount(1, $provider->matchAvailableTopics([$topic]));
        self::assertEquals($topic, current($provider->matchAvailableTopics([$topic])));
    }

    public function testItDoesNotMatchAvailableTopics(): void
    {
        $provider = new Provider('id', new ProviderName('name'));
        $provider->addTopic('newTopic');

        self::assertEmpty($provider->matchAvailableTopics(['otherTopic']));
    }

    public function testItDoesNotAddEmptyTopic(): void
    {
        $provider = new Provider('id', new ProviderName('name'));
        $provider->addTopic('');

        self::assertEmpty($provider->matchAvailableTopics(['']));
    }

    public function testItDoesNotSmokeWhenDuplicatedTopicsAdded(): void
    {
        $provider = new Provider('id', new ProviderName('name'));
        $provider->addTopic($topic = 'newTopic');
        $provider->addTopic($topic);

        self::assertNotEmpty($provider->matchAvailableTopics([$topic]));
        self::assertCount(1, $provider->matchAvailableTopics([$topic]));
        self::assertEquals($topic, current($provider->matchAvailableTopics([$topic])));
    }
}
