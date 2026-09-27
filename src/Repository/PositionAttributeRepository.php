<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;
use App\Entity\PositionAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PositionAttribute>
 */
class PositionAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionAttribute::class);
    }

    /**
     * @return PositionAttribute[]
     */
    public function findForPosition(Position $position): array
    {
        return $this->createQueryBuilder('positionAttribute')
            ->addSelect('attribute')
            ->join('positionAttribute.attribute', 'attribute')
            ->andWhere('positionAttribute.position = :position')
            ->setParameter('position', $position)
            ->orderBy('positionAttribute.displayOrder', 'ASC')
            ->addOrderBy('positionAttribute.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return PositionAttribute[] Returns an array of PositionAttribute objects
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

//    public function findOneBySomeField($value): ?PositionAttribute
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
