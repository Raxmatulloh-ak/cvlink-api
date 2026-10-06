<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserAttributeValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAttributeValue>
 */
class UserAttributeValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAttributeValue::class);
    }

    /**
     * @return UserAttributeValue[]
     */
    public function findForOwner(User $owner): array
    {
        return $this->createQueryBuilder('value')
            ->addSelect('attribute', 'option')
            ->join('value.attribute', 'attribute')
            ->leftJoin('value.option', 'option')
            ->andWhere('value.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User[] $owners
     * @return UserAttributeValue[]
     */
    public function findForOwners(array $owners): array
    {
        if ($owners === []) {
            return [];
        }

        return $this->createQueryBuilder('value')
            ->addSelect('owner', 'attribute', 'option')
            ->join('value.owner', 'owner')
            ->join('value.attribute', 'attribute')
            ->leftJoin('value.option', 'option')
            ->andWhere('value.owner IN (:owners)')
            ->setParameter('owners', $owners)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User[] $owners
     * @return UserAttributeValue[]
     */
    public function findNameValuesForOwners(array $owners): array
    {
        if ($owners === []) {
            return [];
        }

        return $this->createQueryBuilder('value')
            ->addSelect('owner', 'attribute')
            ->join('value.owner', 'owner')
            ->join('value.attribute', 'attribute')
            ->andWhere('value.owner IN (:owners)')
            ->andWhere('attribute.builtinKey IN (:keys)')
            ->setParameter('owners', $owners)
            ->setParameter('keys', ['first_name', 'last_name'])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<string, ?string>
     */
    public function findSalesforceValuesForOwner(User $owner): array
    {
        $values = $this->createQueryBuilder('value')
            ->select('attribute.builtinKey AS builtinKey')
            ->addSelect('value.textValue AS textValue')
            ->join('value.attribute', 'attribute')
            ->andWhere('value.owner = :owner')
            ->andWhere('attribute.builtinKey IN (:keys)')
            ->setParameter('owner', $owner)
            ->setParameter('keys', [
                'first_name',
                'last_name',
                'location',
            ])
            ->getQuery()
            ->getArrayResult();

        return array_column($values, 'textValue', 'builtinKey');
    }

//    /**
//     * @return UserAttributeValue[] Returns an array of UserAttributeValue objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserAttributeValue
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
