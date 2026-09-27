<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Component\UserAttributeValue\UserAttributeValueService;
use App\Entity\User;
use App\Repository\AttributeDefinitionRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserAttributeValueRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class UserProfileAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
        private readonly UserAttributeValueRepository $userAttributeValueRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly UserAttributeValueService $userAttributeValueService,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $owner = $this->userRepository->find($id);

        if ($owner === null) {
            throw new NotFoundHttpException('User not found');
        }

        $user = $this->getUser();

        if (!$user instanceof User || ($owner !== $user && !$this->isGranted('ROLE_ADMIN'))) {
            throw new AccessDeniedHttpException();
        }

        $values = $this->userAttributeValueRepository->findBy(
            ['owner' => $owner],
            ['id' => 'ASC'],
        );

        $builtins = $this->attributeDefinitionRepository->findBuiltinAttributes();
        $fields = [];
        $added = [];

        foreach ($values as $value) {
            $attribute = $value->getAttribute();

            if ($attribute === null) {
                continue;
            }

            $added[$attribute->getId()] = true;
            $fields[] = [
                'attribute' => $attribute,
                'value' => $value,
                'empty' => !$this->userAttributeValueService->isFilled($value),
            ];
        }

        foreach ($builtins as $attribute) {
            if (isset($added[$attribute->getId()])) {
                continue;
            }

            $fields[] = [
                'attribute' => $attribute,
                'value' => null,
                'empty' => true,
            ];
        }

        $projects = $this->projectRepository->findBy(
            ['candidate' => $owner],
            ['startDate' => 'DESC'],
        );

        $json = $this->serializer->serialize([
            'ownerId' => $owner->getId(),
            'fields' => $fields,
            'projects' => $projects,
        ], 'json', [
            'groups' => ['attribute:read', 'value:read', 'project:read'],
        ]);

        return new JsonResponse($json, 200, [], true);
    }
}
