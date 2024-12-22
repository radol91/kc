<?php

declare(strict_types=1);


namespace App\Domain\Provider;


readonly class ProviderName implements \JsonSerializable
{
    public function __construct(
        public string $value,
    ) {}

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
