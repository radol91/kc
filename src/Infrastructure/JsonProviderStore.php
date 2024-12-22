<?php

declare(strict_types=1);


namespace App\Infrastructure;

use App\Domain\Provider\Provider;
use App\Domain\Provider\ProviderName;
use App\Domain\Provider\ProviderStoreInterface;

class JsonProviderStore implements ProviderStoreInterface
{
    public const string PATH_TO_JSON = '/app/public/provider_topics.json';

    /**
     * @var array{
     *     provider_topics: array<string, string>
     * }
     */
    private array $store;

    public function __construct(
        private readonly string $pathToJson = self::PATH_TO_JSON,
    ) {
        if (!file_exists($this->pathToJson)) {
            throw new \RuntimeException('JSON file with providers not found.');
        }

        $jsonContent = file_get_contents($this->pathToJson);
        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('JSON file is malformed.');
        }

        if (!isset($data['provider_topics'])) {
            throw new \RuntimeException('Missing `provider_topics` key in JSON file.');
        }

        $this->store = $data;
    }


    /** @return Provider[] */
    public function getProviders(): array
    {
        $providers = [];
        foreach ($this->store['provider_topics'] as $provider => $topicString) {
            $provider = new Provider($provider, new ProviderName($provider));
            $providerTopics = explode('+', $topicString);
            foreach ($providerTopics as $topic) {
                $provider->addTopic($topic);
            }

            $providers[] = $provider;
        }

        return $providers;
    }
}
