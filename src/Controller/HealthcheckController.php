<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route(stateless: true)]
class HealthcheckController extends AbstractController
{
    #[OA\Get(
        path: '/healthcheck',
        operationId: 'healthcheck',
        summary: 'healthcheck',
        tags: ['Technical'],
        responses: [
            new OA\Response(
                response: 200,
                description: '',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', example: 'ok'),
                    ],
                )
            ),
        ]
    )]
    #[Route(path: '/healthcheck', name: 'healthcheck', methods: [Request::METHOD_GET])]
    public function healthcheck(): JsonResponse
    {
        return $this->json(['status' => 'ok']);
    }
}
