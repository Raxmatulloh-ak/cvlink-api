<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Entity\User;
use App\Enum\UserStatus;
use App\Enum\Theme;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function create(
        string $email,
        string $password,
        array $roles,
        UserStatus $status,
        string $locale,
        Theme $theme,
    ): User {
        $user = new User();
        $hashedPassword = $this->passwordEncoder->hashPassword($user, $password);

        return $user
            ->setEmail($email)
            ->setPassword($hashedPassword)
            ->setRoles($roles)
            ->setStatus($status)
            ->setLocale($locale)
            ->setTheme($theme);
    }

    public function createSocial(string $email): User
    {
        return new User()
            ->setEmail($email)
            ->setRoles(['ROLE_CANDIDATE'])
            ->setStatus(UserStatus::ACTIVE);
    }
}
