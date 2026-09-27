<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Component\User\UserDisplayNameProvider;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserPublicProfileAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserDisplayNameProvider $userDisplayNameProvider,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        $names = $this->userDisplayNameProvider->getForUsers([$user]);

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $names[$user->getId()] ?? null,
        ]);
    }
}
