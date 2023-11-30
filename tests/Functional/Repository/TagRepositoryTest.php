<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Entity\Tag;
use App\Tests\Factory\TagFactory;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectManager;

class TagRepositoryTest extends FunctionalTestCase
{
    private ObjectManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        TagFactory::createMany(3, static fn(int $i) => ['name' => "Tag $i"]);
    }

    public function testFindAll(): void
    {
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tags = $tagRepository->findAll();

        $this->assertIsArray($tags);
        $this->assertNotEmpty($tags);
        $this->assertInstanceOf(Tag::class, $tags[0]);
    }

    public function testFindByCriteria(): void
    {
        $tag = TagFactory::createOne(['name' => 'Tag 1']);

        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tags = $tagRepository->findBy(['name' => $tag->getName()]);

        $this->assertIsArray($tags);
        $this->assertNotEmpty($tags);
    }

    public function testFindOneByCriteria(): void
    {
        $criteria = ['name' => 'Tag 1'];

        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag = $tagRepository->findOneBy($criteria);

        $this->assertInstanceOf(Tag::class, $tag);
    }
}
