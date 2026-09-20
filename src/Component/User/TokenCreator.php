<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Dtos\TokenDto;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class TokenCreator
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function create(User $user): TokenDto
    {
        return new TokenDto(
            $this->jwtManager->create($user),
        );
    }
}
