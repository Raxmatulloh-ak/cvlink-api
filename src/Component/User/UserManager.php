<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Component\Core\AbstractManager;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager extends AbstractManager
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

    public function hashPassword(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
    }
}
