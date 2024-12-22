<?php

declare(strict_types=1);


namespace App\Domain\Provider;

interface ProviderStoreInterface
{
    /**
     * Returns all providers
     *
     * @return Provider[]
     */
    public function getProviders(): array;
}
