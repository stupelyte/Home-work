<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAllOrderedByUpdatedAt(): array
    {
        return $this->createQueryBuilder('article')
            ->orderBy('article.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
