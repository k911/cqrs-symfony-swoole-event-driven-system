<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Repository;

use App\Domain\Review\ReviewComment;
use App\Domain\Review\ReviewCommentId;
use App\Domain\Review\ReviewCommentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReviewComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewComment[]    findAll()
 * @method ReviewComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewCommentRepository extends ServiceEntityRepository implements ReviewCommentRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReviewComment::class);
    }

    public function findById(ReviewCommentId $reviewId): ?ReviewComment
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.id = :val')
            ->setParameter('val', $reviewId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
