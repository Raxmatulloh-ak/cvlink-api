<?php

declare(strict_types=1);

namespace App\Component\Project;

use App\Entity\Project;
use App\Entity\User;
use DateTimeImmutable;

class ProjectFactory
{
    public function create(
        User $candidate,
        string $name,
        DateTimeImmutable $startDate,
        ?DateTimeImmutable $endDate,
        string $description,
    ): Project {
        return new Project()
            ->setCandidate($candidate)
            ->setName($name)
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setDescription($description);
    }
}
