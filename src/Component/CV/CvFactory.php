<?php

declare(strict_types=1);

namespace App\Component\CV;

use App\Entity\CV;
use App\Entity\Position;
use App\Entity\User;
use App\Enum\CvStatus;

class CvFactory
{
    public function create(
        User $candidate,
        Position $position,
    ): CV {
        return new CV()
            ->setCandidate($candidate)
            ->setPosition($position)
            ->setStatus(CvStatus::DRAFT);
    }
}
