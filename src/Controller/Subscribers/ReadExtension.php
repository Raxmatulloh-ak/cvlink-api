<?php

declare(strict_types=1);

namespace App\Controller\Subscribers;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\CV;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserAttributeValue;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class ReadExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->applyFilters($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->applyFilters($queryBuilder, $resourceClass);
    }

    private function applyFilters(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        switch ($resourceClass) {
            case User::class:
                $queryBuilder
                    ->andWhere(sprintf('%s.id = :currentUserId', $rootAlias))
                    ->setParameter('currentUserId', $user->getId());

                break;

            case CV::class:
            case Project::class:
                if ($this->security->isGranted('ROLE_CANDIDATE')) {
                    $queryBuilder
                        ->andWhere(sprintf('%s.candidate = :currentUser', $rootAlias))
                        ->setParameter('currentUser', $user);
                }

                break;

            case UserAttributeValue::class:
                $queryBuilder
                    ->andWhere(sprintf('%s.owner = :currentUser', $rootAlias))
                    ->setParameter('currentUser', $user);

                break;

        }
    }
}
