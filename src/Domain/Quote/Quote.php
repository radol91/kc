<?php

declare(strict_types=1);


namespace App\Domain\Quote;

use Symfony\Component\Serializer\Attribute\Ignore;

readonly class Quote implements \JsonSerializable
{
    public function __construct(
        public float $value,
    ) {}

    public static function createEmpty(): self
    {
        return new Quote(0.0);
    }

    #[Ignore]
    public function isEmpty(): bool
    {
        return 0.0 === $this->value;
    }

    public function jsonSerialize(): float
    {
        return $this->value;
    }
}
