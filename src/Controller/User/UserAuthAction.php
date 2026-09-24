<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Component\User\Dto\TokenDto;
use App\Component\User\TokenCreator;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAuthAction extends AbstractController
{
    public function __invoke(
        User $data,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        TokenCreator $tokenCreator,
    ): TokenDto {
        $user = $userRepository->findOneByEmail((string)$data->getEmail());

        if ($user === null || !$passwordHasher->isPasswordValid($user, (string)$data->getPassword())) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid credentials');
        }

        if ($user->getStatus() === UserStatus::BLOCKED) {
            throw new UnauthorizedHttpException('Bearer', 'User account is blocked');
        }

        return $tokenCreator->create($user);
    }
}
