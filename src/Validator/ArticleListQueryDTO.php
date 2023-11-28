<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Tag;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ArticleListQueryDTO
{
    public function __construct(
        #[Assert\All([
            new Assert\Positive(),
            new EntityExists(class: Tag::class),
        ])]
        public array $tags = [],
    ) {
    }
}
