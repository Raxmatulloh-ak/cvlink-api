<?php

declare(strict_types=1);

namespace App\Component\Position\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PositionUpdateDto extends PositionCreateDto
{
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['position:write'])]
    private ?int $version = null;

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): static
    {
        $this->version = $version;

        return $this;
    }
}
