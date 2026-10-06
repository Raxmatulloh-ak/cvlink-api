<?php

declare(strict_types=1);

namespace App\Component\User\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class GoogleAuthDto
{
    #[Assert\NotBlank]
    #[Groups(['user:write'])]
    private ?string $credential = null;

    public function getCredential(): ?string
    {
        return $this->credential;
    }

    public function setCredential(?string $credential): void
    {
        $this->credential = $credential;
    }
}
