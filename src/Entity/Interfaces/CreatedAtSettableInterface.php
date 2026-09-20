<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use DateTimeImmutable;

interface CreatedAtSettableInterface
{
    public function setCreatedAt(DateTimeImmutable $createdAt): static;
}
