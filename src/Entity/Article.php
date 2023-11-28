<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[OA\Schema(schema: 'Article')]
#[OA\Schema(
    schema: 'ArticlePayload',
    properties: [
        new OA\Property(property: 'title', ref: '#/components/schemas/Article/properties/title'),
        new OA\Property(
            property: 'articleTags', type: 'array', items: new OA\Items(
            properties: [
                new OA\Property(property: 'tag', ref: '#/components/schemas/Tag/properties/id'),
            ],
        )
        ),
    ],
)]
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[UniqueEntity(fields: ['title'])]
class Article
{
    public const GROUP_VIEW = 'article.view';

    #[OA\Property(type: 'integer', example: 1)]
    #[Groups([self::GROUP_VIEW])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[OA\Property(type: 'string', example: 'new article')]
    #[Groups([self::GROUP_VIEW])]
    #[ORM\Column(type: Types::STRING)]
    private string $title;

    #[ORM\OneToMany(
        mappedBy: 'article',
        targetEntity: ArticleTag::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true)
    ]
    private Collection $articleTags;

    public function __construct()
    {
        $this->articleTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /** @return Collection<ArticleTag> */
    public function getArticleTags(): Collection
    {
        return $this->articleTags;
    }

    /** @param Collection<ArticleTag> $articleTags */
    public function setArticleTags(Collection $articleTags): self
    {
        $this->articleTags = $articleTags;

        return $this;
    }

    public function addArticleTag(ArticleTag $articleTag): self
    {
        if (!$this->articleTags->contains($articleTag)) {
            $this->articleTags->add($articleTag);
            $articleTag->setArticle($this);
        }

        return $this;
    }

    public function removeArticleTag(ArticleTag $questionAnswer): self
    {
        if ($this->articleTags->contains($questionAnswer)) {
            $this->articleTags->removeElement($questionAnswer);
        }

        return $this;
    }
}
