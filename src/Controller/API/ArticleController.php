<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\API\Dto\ArticleRequestDto;
use App\Entity\Article;
use App\Exception\InvalidFormDataException;
use App\Form\Type\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/v1/articles', stateless: true)]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ArticleRepository $repository,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/articles',
        operationId: 'articles_list',
        summary: 'Getting articles',
        tags: ['Article'],
        parameters: [
            new OA\Parameter(
                name: 'tags[]',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer'),
                    example: [1, 2]
                ),
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/ArticleWithTags')
                )
            ),
            new OA\Response(ref: '#/components/responses/response_422', response: Response::HTTP_UNPROCESSABLE_ENTITY),
        ]
    )]
    #[Route(name: 'articles_list', methods: [Request::METHOD_GET])]
    public function list(#[MapQueryString] ArticleRequestDto $articleListQueryDTO): JsonResponse
    {
        $articles = $this->repository->findArticlesByTagIds($articleListQueryDTO->tags);

        return $this->json(
            data: $articles,
            context: [AbstractNormalizer::GROUPS => Article::GROUP_ARTICLE_WITH_TAGS]
        );
    }

    #[OA\Get(
        path: '/api/v1/articles/{id}',
        operationId: 'articles_get',
        summary: 'Getting article by id',
        tags: ['Article'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success',
                content: new OA\JsonContent(ref: '#/components/schemas/ArticleWithTags')
            ),
            new OA\Response(ref: '#/components/responses/response_422', response: Response::HTTP_UNPROCESSABLE_ENTITY),
        ]
    )]
    #[Route('/{id<\d{1,9}>}', name: 'articles_get', methods: [Request::METHOD_GET])]
    public function get(Article $article): JsonResponse
    {
        return $this->json(
            data: $article,
            context: [AbstractNormalizer::GROUPS => Article::GROUP_ARTICLE_WITH_TAGS]
        );
    }

    #[OA\Post(
        path: '/api/v1/articles',
        operationId: 'articles_create',
        summary: 'Creating an article',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/ArticlePayload'),
        ),
        tags: ['Article'],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Created',
                content: new OA\JsonContent(ref: '#/components/schemas/Article'),
            ),
            new OA\Response(ref: '#/components/responses/response_422', response: Response::HTTP_UNPROCESSABLE_ENTITY),
        ]
    )]
    #[Route(name: 'articles_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $article = new Article();
        $this->handleForm($article, $request);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json(
            data: $article,
            context: [AbstractNormalizer::GROUPS => [Article::GROUP_VIEW]]
        );
    }

    #[OA\Patch(
        path: '/api/v1/articles/{id}',
        operationId: 'articles_update',
        summary: 'Updating an article',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/ArticlePayload'),
        ),
        tags: ['Article'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Article'),
            ),
            new OA\Response(ref: '#/components/responses/response_404', response: Response::HTTP_NOT_FOUND),
        ]
    )]
    #[Route('/{id<\d{1,9}>}', name: 'articles_update', methods: [Request::METHOD_PATCH])]
    public function update(Article $article, Request $request): JsonResponse
    {
        $this->handleForm($article, $request);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json(
            data: $article,
            context: [AbstractNormalizer::GROUPS => [Article::GROUP_VIEW]]
        );
    }

    #[OA\Delete(
        path: '/api/v1/articles/{id}',
        operationId: 'articles_delete',
        summary: 'Deleting an article',
        tags: ['Article'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(ref: '#/components/responses/response_201', response: Response::HTTP_NO_CONTENT),
            new OA\Response(ref: '#/components/responses/response_404', response: Response::HTTP_NOT_FOUND),
        ]
    )]
    #[Route('/{id<\d{1,9}>}', name: 'articles_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Article $article): Response
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_ACCEPTED);
    }

    private function handleForm(Article $article, Request $request): void
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new InvalidFormDataException($form);
        }
    }
}
