<?php

declare(strict_types=1);


namespace App\Application;

use App\Domain\Provider\Provider;
use App\Domain\Quote\Quote;

readonly class ProviderQuoteDto
{
    public function __construct(
        public Provider $provider,
        public Quote $quote
    ) {}
}
