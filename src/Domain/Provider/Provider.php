<?php

declare(strict_types=1);


namespace App\Domain\Provider;

class Provider
{
    /** @var string[] */
    private array $topics;
    public function __construct(
        public readonly string $id,
        public ProviderName $name,
    ) {
        $this->topics = [];
    }

    public function addTopic(string $topic): void
    {
        if (empty($topic)) {
            return;
        }

        if (!in_array($topic, $this->topics)) {
            $this->topics[] = $topic;
        }
    }

    /**
     * @param string[] $consideredTopics
     *
     * @return string[]
     */
    public function matchAvailableTopics(array $consideredTopics): array
    {
        return array_intersect($this->topics, $consideredTopics);
    }
}
