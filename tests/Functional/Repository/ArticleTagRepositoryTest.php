<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Entity\ArticleTag;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\ArticleTagFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectManager;

class ArticleTagRepositoryTest extends FunctionalTestCase
{
    private ObjectManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        ArticleTagFactory::createMany(
            3,
            static fn(int $i) => [
                'article' => ArticleFactory::createOne(),
                'tag' => TagFactory::createOne(),
            ]
        );
    }

    public function testFindAll(): void
    {
        $articleTagRepository = $this->entityManager->getRepository(ArticleTag::class);
        $articleTags = $articleTagRepository->findAll();

        $this->assertIsArray($articleTags);
        $this->assertNotEmpty($articleTags);
        $this->assertInstanceOf(ArticleTag::class, $articleTags[0]);
    }

    public function testFindByCriteria(): void
    {
        $article = ArticleFactory::createOne();
        $tag = TagFactory::createOne();
        ArticleTagFactory::createOne(['article' => $article, 'tag' => $tag]);

        $articleTagRepository = $this->entityManager->getRepository(ArticleTag::class);
        $articleTags = $articleTagRepository->findBy(['article' => $article->getId(), 'tag' => $tag->getId()]);

        $this->assertIsArray($articleTags);
        $this->assertNotEmpty($articleTags);
    }

    public function testFindOneByCriteria(): void
    {
        $article = ArticleFactory::createOne();
        $tag = TagFactory::createOne();
        ArticleTagFactory::createOne(['article' => $article, 'tag' => $tag]);

        $articleTagRepository = $this->entityManager->getRepository(ArticleTag::class);
        $articleTag = $articleTagRepository->findOneBy(['article' => $article->getId(), 'tag' => $tag->getId()]);

        $this->assertInstanceOf(ArticleTag::class, $articleTag);
    }
}
