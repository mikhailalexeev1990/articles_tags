<?php

declare(strict_types=1);

namespace App\Controller\Rest\Dto;

use App\Entity\Tag;
use App\Validator\EntityExistsConstraint;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ArticleRequestDto
{
    public function __construct(
        #[Assert\All([
            new Assert\Positive(),
            new EntityExistsConstraint(class: Tag::class),
        ])]
        public array $tags = [],
    ) {
    }
}
