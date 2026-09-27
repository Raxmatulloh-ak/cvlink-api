<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;
use App\Enum\CvStatus;
use App\Enum\PositionAccessType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Position>
 */
class PositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    /**
     * @return Position[]
     */
    public function findForBrowse(bool $publicOnly): array
    {
        $queryBuilder = $this->createQueryBuilder('position')
            ->orderBy('COALESCE(position.updatedAt, position.createdAt)', 'DESC')
            ->addOrderBy('position.id', 'DESC');

        if ($publicOnly) {
            $queryBuilder
                ->andWhere('position.accessType = :accessType')
                ->setParameter('accessType', PositionAccessType::PUBLIC);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function deleteByIdAndVersion(int $id, int $version): int
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(Position::class, 'position')
            ->where('position.id = :id')
            ->andWhere('position.version = :version')
            ->setParameter('id', $id)
            ->setParameter('version', $version)
            ->getQuery()
            ->execute();
    }

    /**
     * @return Position[]
     */
    public function findLatestForDashboard(bool $publicOnly, int $limit = 50): array
    {
        $queryBuilder = $this->createQueryBuilder('position')
            ->orderBy('COALESCE(position.updatedAt, position.createdAt)', 'DESC')
            ->addOrderBy('position.id', 'DESC')
            ->setMaxResults($limit);

        if ($publicOnly) {
            $queryBuilder
                ->andWhere('position.accessType = :accessType')
                ->setParameter('accessType', PositionAccessType::PUBLIC);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array<int, array{0: Position, submittedCvs: string|int}>
     */
    public function findPopularForDashboard(bool $publicOnly, int $limit = 50): array
    {
        $queryBuilder = $this->createQueryBuilder('position')
            ->addSelect('COUNT(cv.id) AS submittedCvs')
            ->leftJoin('position.cvs', 'cv', 'WITH', 'cv.status = :status')
            ->setParameter('status', CvStatus::PUBLISHED)
            ->groupBy('position.id')
            ->orderBy('submittedCvs', 'DESC')
            ->addOrderBy('position.id', 'DESC')
            ->setMaxResults($limit);

        if ($publicOnly) {
            $queryBuilder
                ->andWhere('position.accessType = :accessType')
                ->setParameter('accessType', PositionAccessType::PUBLIC);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Position[]
     */
    public function findForTagCloud(bool $publicOnly, int $limit = 200): array
    {
        $queryBuilder = $this->createQueryBuilder('position')
            ->orderBy('position.id', 'DESC')
            ->setMaxResults($limit);

        if ($publicOnly) {
            $queryBuilder
                ->andWhere('position.accessType = :accessType')
                ->setParameter('accessType', PositionAccessType::PUBLIC);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * PostgreSQL native full-text search.
     *
     * @return int[]
     */
    public function searchIds(string $query, bool $publicOnly, int $limit = 30): array
    {
        $sql = <<<'SQL'
            SELECT id
            FROM position
            WHERE to_tsvector('simple', COALESCE(title, '') || ' ' || COALESCE(description, ''))
                @@ websearch_to_tsquery('simple', :query)
        SQL;

        if ($publicOnly) {
            $sql .= ' AND access_type = :accessType';
        }

        $sql .= ' ORDER BY id DESC LIMIT '.$limit;
        $parameters = ['query' => $query];

        if ($publicOnly) {
            $parameters['accessType'] = PositionAccessType::PUBLIC->value;
        }

        $rows = $this->getEntityManager()->getConnection()->fetchFirstColumn($sql, $parameters);

        return array_map('intval', $rows);
    }

    /**
     * @param int[] $ids
     *
     * @return Position[]
     */
    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $positions = $this->createQueryBuilder('position')
            ->andWhere('position.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        $order = array_flip($ids);

        usort($positions, static fn (Position $left, Position $right): int =>
            ($order[$left->getId()] ?? PHP_INT_MAX) <=> ($order[$right->getId()] ?? PHP_INT_MAX)
        );

        return $positions;
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('position')
            ->select('COUNT(position.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Position[] Returns an array of Position objects
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

//    public function findOneBySomeField($value): ?Position
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
