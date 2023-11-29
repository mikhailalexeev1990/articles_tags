<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find(int $id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param int[] $tagIds
     * @return Article[]
     */
    public function findArticlesByTagIds(array $tagIds): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.articleTags', 'at')
            ->leftJoin('at.tag', 't')
            ->where('t.id IN (:tagIds)')
            ->setParameter('tagIds', $tagIds)
            ->groupBy('a.id')
            ->having('COUNT(DISTINCT t.id) = :tagCount')
            ->setParameter('tagCount', count($tagIds))
            ->getQuery()
            ->getResult();
    }
}
