<?php

declare(strict_types=1);


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QuotesController extends AbstractController
{
    #[Route(path: '/quotes', methods: ['POST'])]
    public function getQuotes(
        Request $request
    ): Response {
        return $this->json([]);
    }
}
