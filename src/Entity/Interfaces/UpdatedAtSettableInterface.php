<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use DateTimeImmutable;

interface UpdatedAtSettableInterface
{
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static;
}
