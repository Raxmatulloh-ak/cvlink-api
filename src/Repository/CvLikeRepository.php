<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CV;
use App\Entity\CvLike;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CvLike>
 */
class CvLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CvLike::class);
    }

    public function countForCv(CV $cv): int
    {
        return $this->count(['cv' => $cv]);
    }

    public function isLikedBy(CV $cv, User $viewer): bool
    {
        return $this->findOneBy(['cv' => $cv, 'recruiter' => $viewer]) !== null;
    }

    /**
     * @param CV[] $cvs
     * @return array<int, int>
     */
    public function countForCvs(array $cvs): array
    {
        if ($cvs === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('like')
            ->select('IDENTITY(like.cv) AS cvId', 'COUNT(like.id) AS likes')
            ->andWhere('like.cv IN (:cvs)')
            ->setParameter('cvs', $cvs)
            ->groupBy('like.cv')
            ->getQuery()
            ->getArrayResult();

        $counts = [];

        foreach ($rows as $row) {
            $counts[(int) $row['cvId']] = (int) $row['likes'];
        }

        return $counts;
    }

//    /**
//     * @return CvLike[] Returns an array of CvLike objects
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

//    public function findOneBySomeField($value): ?CvLike
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
