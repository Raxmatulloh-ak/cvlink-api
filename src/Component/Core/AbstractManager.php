<?php

declare(strict_types=1);

namespace App\Component\Core;

use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(object $entity, bool $needToFlush = false): void
    {
        $this->updateCreatedOrUpdatedDates($entity);

        $this->getEntityManager()->persist($entity);

        if ($needToFlush) {
            $this->entityManager->flush();
        }
    }

    public function remove(object $entity, bool $needToFlush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($needToFlush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    private function updateCreatedOrUpdatedDates(object $entity): void
    {
        if ($entity->getId() === null && $entity instanceof CreatedAtSettableInterface) {
            $entity->setCreatedAt(new DateTimeImmutable());
        } elseif ($entity->getId() !== null && $entity instanceof UpdatedAtSettableInterface) {
            $entity->setUpdatedAt(new DateTimeImmutable());
        }
    }
}
