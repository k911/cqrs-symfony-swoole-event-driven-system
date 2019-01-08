<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Repository;

use App\Domain\Review\Review;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository implements ReviewRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findById(ReviewId $reviewId): ?Review
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.id = :val')
            ->setParameter('val', $reviewId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
