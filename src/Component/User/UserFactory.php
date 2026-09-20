<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Entity\User;
use App\Enum\UserStatus;
use App\Enum\Theme;

class UserFactory
{
    public function create(
        string $email,
        array $roles,
        UserStatus $status,
        string $locale,
        Theme $theme,
    ): User {
        return new User()
            ->setEmail($email)
            ->setRoles($roles)
            ->setStatus($status)
            ->setLocale($locale)
            ->setTheme($theme);
    }
}
