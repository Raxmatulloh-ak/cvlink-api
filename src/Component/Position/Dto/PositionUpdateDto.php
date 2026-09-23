<?php

declare(strict_types=1);

namespace App\Component\Position\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PositionUpdateDto extends PositionWriteDto
{
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['position:write'])]
    private ?int $version = null;

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }
}
