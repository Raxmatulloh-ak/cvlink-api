<?php

declare(strict_types=1);

namespace App\Controller\AttributeDefinition;

use App\Entity\User;
use App\Repository\AttributeDefinitionRepository;
use BackedEnum;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RecentAttributesAction extends AbstractController
{
    public function __construct(
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $rows = $this->isGranted('ROLE_RECRUITER') || $this->isGranted('ROLE_ADMIN')
            ? $this->attributeDefinitionRepository->findRecentForRecruiter()
            : $this->attributeDefinitionRepository->findRecentForCandidate($user);

        $items = [];

        foreach ($rows as $row) {
            $valueType = $row['valueType'];
            $usedAt = $row['usedAt'];

            $items[] = [
                'id' => (int) $row['id'],
                'name' => $row['name'],
                'valueType' => $valueType instanceof BackedEnum ? $valueType->value : $valueType,
                'category' => [
                    'id' => (int) $row['categoryId'],
                    'name' => $row['categoryName'],
                ],
                'usedAt' => $usedAt instanceof DateTimeInterface ? $usedAt->format(DATE_ATOM) : $usedAt,
            ];
        }

        return new JsonResponse(['items' => $items]);
    }
}
