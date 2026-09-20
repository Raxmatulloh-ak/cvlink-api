<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Entity\User;
use App\Enum\Theme;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserCreateAction
{
    public function __invoke(
        User $data,
        UserFactory $userFactory,
        UserManager $userManager,
        UserRepository $userRepository,
    ): User {
        $email = strtolower(trim((string) $data->getEmail()));

        if ($userRepository->findOneByEmail($email)) {
            throw new BadRequestHttpException('Email already taken');
        }

        $user = $userFactory->create(
            $email,
            (string) $data->getPassword(),
            ['ROLE_CANDIDATE'],
            UserStatus::ACTIVE,
            'en',
            Theme::Light,
        );

        $userManager->save($user, true);

        return $user;
    }
}
