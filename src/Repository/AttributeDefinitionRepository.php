<?php

namespace App\Repository;

use App\Entity\AttributeDefinition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttributeDefinition>
 */
class AttributeDefinitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeDefinition::class);
    }

    public function findOneByName(string $name): ?AttributeDefinition
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @return AttributeDefinition[]
     */
    public function findBuiltinAttributes(): array
    {
        return $this->createQueryBuilder('attribute')
            ->andWhere('attribute.builtinKey IS NOT NULL')
            ->orderBy('attribute.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return AttributeDefinition[] Returns an array of AttributeDefinition objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AttributeDefinition
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
