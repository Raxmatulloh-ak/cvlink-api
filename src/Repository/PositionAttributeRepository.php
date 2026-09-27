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

    /**
     * @param Position[] $positions
     * @return array<int, int>
     */
    public function countForPositions(array $positions): array
    {
        if ($positions === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('positionAttribute')
            ->select(
                'IDENTITY(positionAttribute.position) AS positionId',
                'COUNT(positionAttribute.id) AS attributeCount',
            )
            ->andWhere('positionAttribute.position IN (:positions)')
            ->setParameter('positions', $positions)
            ->groupBy('positionAttribute.position')
            ->getQuery()
            ->getArrayResult();

        $counts = [];

        foreach ($rows as $row) {
            $counts[(int) $row['positionId']] = (int) $row['attributeCount'];
        }

        return $counts;
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
