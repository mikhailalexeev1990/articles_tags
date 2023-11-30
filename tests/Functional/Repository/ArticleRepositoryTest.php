<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Entity\Article;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\ArticleTagFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectManager;


class ArticleRepositoryTest extends FunctionalTestCase
{
    private ObjectManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getmanager();

        ArticleFactory::createMany(
            3,
            static fn(int $i) => ['title' => "Article $i"]
        );
    }

    public function testFindAll(): void
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $articles = $articleRepository->findAll();

        $this->assertIsArray($articles);
        $this->assertNotEmpty($articles);
        $this->assertInstanceOf(Article::class, $articles[0]);
    }

    public function testFindByCriteria(): void
    {
        $criteria = ['title' => 'Article 1'];

        $articleRepository = $this->entityManager->getRepository(Article::class);
        $articles = $articleRepository->findBy($criteria);

        $this->assertIsArray($articles);
        $this->assertNotEmpty($articles);
    }

    public function testFindOneByCriteria(): void
    {
        $criteria = ['title' => 'Article 1'];

        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->findOneBy($criteria);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testFindArticlesByTagIds(): void
    {
        $tag1 = TagFactory::createOne(['name' => 'Tag 1']);
        $tag2 = TagFactory::createOne(['name' => 'Tag 2']);
        $tag3 = TagFactory::createOne(['name' => 'Tag 3']);

        $article1 = ArticleFactory::createOne(['title' => 'Article 1']);
        $article2 = ArticleFactory::createOne(['title' => 'Article 2']);
        $article3 = ArticleFactory::createOne(['title' => 'Article 3']);

        ArticleTagFactory::createOne(['article' => $article1, 'tag' => $tag1]);
        ArticleTagFactory::createOne(['article' => $article1, 'tag' => $tag2]);
        ArticleTagFactory::createOne(['article' => $article1, 'tag' => $tag3]);
        ArticleTagFactory::createOne(['article' => $article2, 'tag' => $tag1]);
        ArticleTagFactory::createOne(['article' => $article2, 'tag' => $tag2]);
        ArticleTagFactory::createOne(['article' => $article3, 'tag' => $tag2]);

        $articleRepository = $this->entityManager->getRepository(Article::class);
        $tagIds = [$tag1->getId(), $tag2->getId(), $tag3->getId()];
        $articles = $articleRepository->findArticlesByTagIds($tagIds);

        $this->assertIsArray($articles);
        $this->assertCount(1, $articles);
        $this->assertInstanceOf(Article::class, $articles[0]);
    }
}
