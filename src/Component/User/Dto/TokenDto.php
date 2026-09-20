<?php

declare(strict_types=1);

namespace App\Component\User\Dtos;

use Symfony\Component\Serializer\Attribute\Groups;

class TokenDto
{
    public function __construct(
        #[Groups(['user:read'])]
        private readonly string $accessToken,
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}
