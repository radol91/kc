<?php

declare(strict_types=1);


namespace App\Application;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetProvidersQuotesUseCase
{
    /** @param array<string, int> $topics */
    public function __construct(
        #[Assert\All(constraints: [
            new Assert\Type('integer'),
            new Assert\GreaterThanOrEqual(0)
        ])]
        public array $topics
    ) {}

    /** @param string[] $topics */
    public function getTotalByTopics(array $topics): float
    {
        return array_sum(array_map(fn($key) => $this->topics[$key] ?? 0, $topics));
    }
}
