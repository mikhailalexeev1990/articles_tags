<?php

namespace App\Controller;

use OpenApi\Annotations\OpenApi;
use OpenApi\Attributes as OA;
use OpenApi\Generator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Info(
    version: '1.0.0',
    title: 'Test task API',
)]
#[OA\Server(
    url: 'http://localhost:8001',
    description: 'Local server',
)]
#[OA\Response(response: 'response_200', description: 'OK')]
#[OA\Response(response: 'response_201', description: 'Created')]
#[OA\Response(response: 'response_204', description: 'No Content')]
#[OA\Response(response: 'response_404', description: 'Not Found')]
#[OA\Response(response: 'response_422', description: 'Unprocessable entity')]
class SwaggerController extends AbstractController
{
    #[Route(path: '/api/doc', name: 'swagger_ui', methods: [Request::METHOD_GET])]
    public function getUI(): Response
    {
        return $this->render(
            '/swagger/swagger.html.twig',
            [
                'url' => $this->generateUrl('swagger_json'),
            ]
        );
    }

    #[Route(path: '/api/doc.json', name: 'swagger_json', methods: [Request::METHOD_GET])]
    public function getJson(): Response
    {
        $openApi = Generator::scan(
            sources: [
                __DIR__ . '/../Controller',
                __DIR__ . '/../Entity',
            ],
            options: [
                'version' => OpenApi::VERSION_3_0_0,
            ]
        );

        return new Response($openApi?->toJson());
    }
}
