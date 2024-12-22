<?php

declare(strict_types=1);


namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProvidersQuotesTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function testItReturnsProvidersQuotesInCorrectJsonShape(): void
    {
        $this->callProvidersQuotesEndpoint();
        self::assertResponseIsSuccessful();
        $this->assertJson($json = $this->client->getResponse()->getContent());
        $results = json_decode($json, true);
        self::assertNotEmpty($results);
        $item = current($results);
        self::assertArrayHasKey('provider', $item);
        self::assertArrayHasKey('quote', $item);
    }

    public function testItCalculatesCorrectQuotes(): void
    {
        $this->callProvidersQuotesEndpoint();
        self::assertResponseIsSuccessful();
        $this->assertJson($json = $this->client->getResponse()->getContent());
        $results = json_decode($json, true);
        self::assertCount(3, $results);
        $mappedResults = array_combine(
            array_column($results, 'provider'),
            array_column($results, 'quote'),
        );

        $expectedResults = [
            'provider_a' => 8,
            'provider_b' => 5,
            'provider_c' => 10,
        ];

        self::assertEquals($expectedResults, $mappedResults);
    }

    private function callProvidersQuotesEndpoint(): void
    {
        $this->client->request(
            method: 'POST',
            uri: '/quotes',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode(
                [
                    'topics' => [
                        'reading' => 20,
                        'math' => 50,
                        'science' => 30,
                        'history' => 15,
                        'art' => 10,
                    ]
                ]
            )
        );
    }

    protected function tearDown(): void
    {
        unset($this->client);

        parent::tearDown();
    }
}
