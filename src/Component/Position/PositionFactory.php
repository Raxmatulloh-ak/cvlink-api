<?php

declare(strict_types=1);

namespace App\Component\Position;

use App\Entity\Position;
use App\Enum\PositionAccessType;

class PositionFactory
{
    public function create(
        string $title,
        ?string $description,
        PositionAccessType $accessMode,
        int $maxProjects,
    ): Position {
        return new Position()
            ->setTitle($title)
            ->setDescription($description)
            ->setAccessType($accessMode)
            ->setMaxProjects($maxProjects);
    }
}
