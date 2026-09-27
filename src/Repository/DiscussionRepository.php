<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Discussion;
use App\Entity\Position;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Discussion>
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    /**
     * @return Discussion[]
     */
    public function findAfter(Position $position, int $after): array
    {
        return $this->createQueryBuilder('discussion')
            ->addSelect('author')
            ->join('discussion.author', 'author')
            ->andWhere('discussion.position = :position')
            ->andWhere('discussion.id > :after')
            ->setParameter('position', $position)
            ->setParameter('after', $after)
            ->orderBy('discussion.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Discussion[] Returns an array of Discussion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Discussion
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
