<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[OA\Schema(schema: 'Tag')]
#[OA\Schema(
    schema: 'TagPayload',
    properties: [
        new OA\Property(property: 'name', ref: '#/components/schemas/Tag/properties/name'),
    ],
)]
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[UniqueEntity(fields: ['name'])]
class Tag
{
    public const GROUP_VIEW = 'tag.view';

    #[OA\Property(type: 'integer', example: 1)]
    #[Groups([self::GROUP_VIEW])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[OA\Property(type: 'string', example: 'tag 1')]
    #[Groups([self::GROUP_VIEW])]
    #[ORM\Column(type: Types::STRING)]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
