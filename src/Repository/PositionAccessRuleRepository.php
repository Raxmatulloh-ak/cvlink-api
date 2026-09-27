<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;
use App\Entity\PositionAccessRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PositionAccessRule>
 */
class PositionAccessRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionAccessRule::class);
    }

    /**
     * @return PositionAccessRule[]
     */
    public function findForPosition(Position $position): array
    {
        return $this->createQueryBuilder('rule')
            ->addSelect('attribute', 'option')
            ->join('rule.attribute', 'attribute')
            ->leftJoin('rule.option', 'option')
            ->andWhere('rule.position = :position')
            ->setParameter('position', $position)
            ->orderBy('rule.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Position[] $positions
     *
     * @return PositionAccessRule[]
     */
    public function findForPositions(array $positions): array
    {
        if ($positions === []) {
            return [];
        }

        return $this->createQueryBuilder('rule')
            ->addSelect('position', 'attribute', 'option')
            ->join('rule.position', 'position')
            ->join('rule.attribute', 'attribute')
            ->leftJoin('rule.option', 'option')
            ->andWhere('rule.position IN (:positions)')
            ->setParameter('positions', $positions)
            ->orderBy('rule.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return PositionAccessRule[] Returns an array of PositionAccessRule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PositionAccessRule
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
