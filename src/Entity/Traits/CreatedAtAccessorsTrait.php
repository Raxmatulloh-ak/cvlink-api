<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTimeImmutable;

trait CreatedAtAccessorsTrait
{
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
