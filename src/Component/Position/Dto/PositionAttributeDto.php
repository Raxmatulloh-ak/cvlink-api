<?php

declare(strict_types=1);

namespace App\Component\Position\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PositionAttributeDto
{
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['position:write'])]
    private ?int $attributeId = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Groups(['position:write'])]
    private ?int $displayOrder = null;

    public function getAttributeId(): ?int
    {
        return $this->attributeId;
    }

    public function setAttributeId(
        ?int $attributeId,
    ): static {
        $this->attributeId = $attributeId;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(
        ?int $displayOrder,
    ): static {
        $this->displayOrder = $displayOrder;

        return $this;
    }
}
