<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AttributeDefinition;
use App\Entity\User;
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

    /**
     * @return AttributeDefinition[]
     */
    public function findForLookup(?string $prefix, ?int $categoryId, int $limit = 30): array
    {
        $queryBuilder = $this->createQueryBuilder('attribute')
            ->addSelect('category')
            ->join('attribute.category', 'category')
            ->orderBy('attribute.name', 'ASC')
            ->setMaxResults($limit);

        if ($prefix !== null && $prefix !== '') {
            $queryBuilder
                ->andWhere('LOWER(attribute.name) LIKE LOWER(:prefix)')
                ->setParameter('prefix', $prefix.'%');
        }

        if ($categoryId !== null) {
            $queryBuilder
                ->andWhere('category.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findRecentForRecruiter(int $limit = 20): array
    {
        return $this->createQueryBuilder('attribute')
            ->select(
                'attribute.id AS id',
                'attribute.name AS name',
                'attribute.valueType AS valueType',
                'category.id AS categoryId',
                'category.name AS categoryName',
                'MAX(COALESCE(position.updatedAt, position.createdAt)) AS usedAt',
            )
            ->join('attribute.category', 'category')
            ->join('App\\Entity\\PositionAttribute', 'positionAttribute', 'WITH', 'positionAttribute.attribute = attribute')
            ->join('positionAttribute.position', 'position')
            ->groupBy('attribute.id, attribute.name, attribute.valueType, category.id, category.name')
            ->orderBy('usedAt', 'DESC')
            ->addOrderBy('attribute.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    public function findRecentForCandidate(User $candidate, int $limit = 20): array
    {
        return $this->createQueryBuilder('attribute')
            ->select(
                'attribute.id AS id',
                'attribute.name AS name',
                'attribute.valueType AS valueType',
                'category.id AS categoryId',
                'category.name AS categoryName',
                'MAX(COALESCE(value.updatedAt, value.createdAt)) AS usedAt',
            )
            ->join('attribute.category', 'category')
            ->join('attribute.userAttributeValues', 'value')
            ->andWhere('value.owner = :candidate')
            ->setParameter('candidate', $candidate)
            ->groupBy('attribute.id, attribute.name, attribute.valueType, category.id, category.name')
            ->orderBy('usedAt', 'DESC')
            ->addOrderBy('attribute.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
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
