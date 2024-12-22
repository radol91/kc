<?php

declare(strict_types=1);


namespace App\Presentation;

use App\Application\GetProvidersQuotesUseCase;
use App\Application\ProviderQuoteDto;
use App\Application\ProvidersQuotesService;
use App\Domain\Quote\Exception\QuoteCalculatorConditionNotHandledException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ProvidersQuotesController extends AbstractController
{
    #[Route(path: '/quotes', methods: ['POST'])]
    public function getQuotes(
        #[MapRequestPayload(
            acceptFormat: 'json',
            serializationContext: [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ],
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST
        )] GetProvidersQuotesUseCase $getProvidersQuotesUseCase,
        ProvidersQuotesService $providerQuoteService,
    ): Response {
        try {
            return $this->json(
                array_map(static fn(ProviderQuoteDto $dto) => [
                    'provider' => $dto->provider->name,
                    'quote' => $dto->quote->value,
                ], $providerQuoteService->getProvidersQuotes($getProvidersQuotesUseCase))
            );
        } catch (QuoteCalculatorConditionNotHandledException $exception) {
            throw new HttpException(
                statusCode: 501,
                message: $exception->getMessage(),
                previous: $exception
            );
        }
    }
}
