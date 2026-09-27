<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;
use App\Entity\PositionTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PositionTag>
 */
class PositionTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionTag::class);
    }

    /**
     * @return int[]
     */
    public function findTagIds(Position $position): array
    {
        $rows = $this->createQueryBuilder('positionTag')
            ->select('tag.id')
            ->join('positionTag.tag', 'tag')
            ->andWhere('positionTag.position = :position')
            ->setParameter('position', $position)
            ->getQuery()
            ->getArrayResult();

        return array_column($rows, 'id');
    }

    /**
     * @param Position[] $positions
     *
     * @return array<int, array{id: int, name: string, positions: int}>
     */
    public function countForPositions(array $positions): array
    {
        if ($positions === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('positionTag')
            ->select('tag.id AS id', 'tag.name AS name', 'COUNT(DISTINCT positionTag.position) AS positions')
            ->join('positionTag.tag', 'tag')
            ->andWhere('positionTag.position IN (:positions)')
            ->setParameter('positions', $positions)
            ->groupBy('tag.id, tag.name')
            ->orderBy('positions', 'DESC')
            ->addOrderBy('tag.name', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): array => [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'positions' => (int) $row['positions'],
        ], $rows);
    }

//    /**
//     * @return PositionTag[] Returns an array of PositionTag objects
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

//    public function findOneBySomeField($value): ?PositionTag
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
