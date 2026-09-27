<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CV;
use App\Entity\User;
use App\Enum\CvStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CV>
 */
class CVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CV::class);
    }

    /**
     * @return CV[]
     */
    public function findForList(
        User $user,
        bool $isAdmin,
        bool $isRecruiter,
        ?int $positionId,
        ?int $candidateId,
        int $page,
        int $limit,
    ): array {
        $queryBuilder = $this->createQueryBuilder('cv')
            ->addSelect('candidate', 'position')
            ->join('cv.candidate', 'candidate')
            ->join('cv.position', 'position')
            ->orderBy('cv.createdAt', 'DESC')
            ->addOrderBy('cv.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit + 1);

        if (!$isAdmin) {
            if ($isRecruiter) {
                $queryBuilder
                    ->andWhere('cv.status = :status')
                    ->setParameter('status', CvStatus::PUBLISHED);
            } else {
                $queryBuilder
                    ->andWhere('cv.candidate = :candidate')
                    ->setParameter('candidate', $user);
            }
        }

        if ($positionId !== null) {
            $queryBuilder
                ->andWhere('position.id = :positionId')
                ->setParameter('positionId', $positionId);
        }

        if ($candidateId !== null) {
            $queryBuilder
                ->andWhere('candidate.id = :candidateId')
                ->setParameter('candidateId', $candidateId);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return int[]
     */
    public function searchIds(
        string $query,
        User $viewer,
        bool $isAdmin,
        bool $isRecruiter,
        int $limit = 30,
    ): array {
        $sql = <<<'SQL'
            SELECT cv.id
            FROM cv
            INNER JOIN position ON position.id = cv.position_id
            WHERE (
                to_tsvector('simple', COALESCE(position.title, '') || ' ' || COALESCE(position.description, ''))
                    @@ websearch_to_tsquery('simple', :query)
                OR EXISTS (
                    SELECT 1
                    FROM user_attribute_value value
                    INNER JOIN position_attribute position_attribute
                        ON position_attribute.attribute_id = value.attribute_id
                    WHERE position_attribute.position_id = position.id
                        AND value.owner_id = cv.candidate_id
                        AND to_tsvector('simple', COALESCE(value.text_value, ''))
                            @@ websearch_to_tsquery('simple', :query)
                )
            )
        SQL;

        $parameters = [
            'query' => $query,
        ];

        if (!$isAdmin) {
            if ($isRecruiter) {
                $sql .= ' AND cv.status = :status';
                $parameters['status'] = CvStatus::PUBLISHED->value;
            } else {
                $sql .= ' AND cv.candidate_id = :candidateId';
                $parameters['candidateId'] = $viewer->getId();
            }
        }

        $sql .= ' ORDER BY cv.id DESC LIMIT '.$limit;

        $rows = $this->getEntityManager()->getConnection()->fetchFirstColumn($sql, $parameters);

        return array_map('intval', $rows);
    }

    /**
     * @param int[] $ids
     *
     * @return CV[]
     */
    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $cvs = $this->createQueryBuilder('cv')
            ->addSelect('candidate', 'position')
            ->join('cv.candidate', 'candidate')
            ->join('cv.position', 'position')
            ->andWhere('cv.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        $order = array_flip($ids);

        usort($cvs, static fn (CV $left, CV $right): int =>
            ($order[$left->getId()] ?? PHP_INT_MAX) <=> ($order[$right->getId()] ?? PHP_INT_MAX)
        );

        return $cvs;
    }

    public function countCreatedSince(\DateTimeImmutable $since): int
    {
        return (int) $this->createQueryBuilder('cv')
            ->select('COUNT(cv.id)')
            ->andWhere('cv.createdAt >= :since')
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPublished(): int
    {
        return (int) $this->createQueryBuilder('cv')
            ->select('COUNT(cv.id)')
            ->andWhere('cv.status = :status')
            ->setParameter('status', CvStatus::PUBLISHED)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return CV[] Returns an array of CV objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CV
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
