<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTimeImmutable;

trait UpdatedAtAccessorsTrait
{
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
