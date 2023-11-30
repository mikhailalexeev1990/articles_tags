<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Entity\Tag;
use App\Exception\InvalidFormDataException;
use App\Form\Type\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route('/api/v1/tags', stateless: true)]
class TagController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[OA\Post(
        path: '/api/v1/tags',
        operationId: 'tags_create',
        summary: 'Creating a tag',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/TagPayload'),
        ),
        tags: ['Tag'],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Created',
                content: new OA\JsonContent(ref: '#/components/schemas/Tag'),
            ),
            new OA\Response(ref: '#/components/responses/response_422', response: Response::HTTP_UNPROCESSABLE_ENTITY),
        ]
    )]
    #[Route(name: 'tags_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $tag = new Tag();
        $this->handleForm($tag, $request);

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $this->json(
            data: $tag,
            context: [AbstractNormalizer::GROUPS => [Tag::GROUP_VIEW]]
        );
    }

    #[OA\Patch(
        path: '/api/v1/tags/{id}',
        operationId: 'tags_update',
        summary: 'Updating a tag',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/TagPayload'),
        ),
        tags: ['Tag'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Tag'),
            ),
            new OA\Response(ref: '#/components/responses/response_404', response: Response::HTTP_NOT_FOUND),
            new OA\Response(ref: '#/components/responses/response_422', response: Response::HTTP_UNPROCESSABLE_ENTITY),
        ]
    )]
    #[Route('/{id<\d{1,9}>}', name: 'tags_update', methods: [Request::METHOD_PATCH])]
    public function update(Tag $tag, Request $request): JsonResponse
    {
        $this->handleForm($tag, $request);

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $this->json(
            data: $tag,
            context: [AbstractNormalizer::GROUPS => [Tag::GROUP_VIEW]]
        );
    }

    private function handleForm(Tag $tag, Request $request): void
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new InvalidFormDataException($form);
        }
    }
}
