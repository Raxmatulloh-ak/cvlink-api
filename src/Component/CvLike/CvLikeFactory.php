<?php

declare(strict_types=1);

namespace App\Component\CvLike;

use App\Entity\CV;
use App\Entity\CvLike;
use App\Entity\User;

class CvLikeFactory
{
    public function create(
        CV $cv,
        User $recruiter,
    ): CvLike {
        return new CvLike()
            ->setCv($cv)
            ->setRecruiter($recruiter);
    }
}
