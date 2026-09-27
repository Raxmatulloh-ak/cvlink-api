<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @param int[] $tagIds
     * @return Project[]
     */
    public function findForCv(User $candidate, array $tagIds, int $limit): array
    {
        if ($limit <= 0) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('project')
            ->andWhere('project.candidate = :candidate')
            ->setParameter('candidate', $candidate)
            ->orderBy('project.startDate', 'DESC')
            ->addOrderBy('project.id', 'DESC')
            ->setMaxResults($limit);

        if ($tagIds !== []) {
            $queryBuilder
                ->distinct()
                ->join('project.projectTags', 'filterProjectTag')
                ->join('filterProjectTag.tag', 'filterTag')
                ->andWhere('filterTag.id IN (:tagIds)')
                ->setParameter('tagIds', $tagIds);
        }

        return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return Project[] Returns an array of Project objects
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

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
