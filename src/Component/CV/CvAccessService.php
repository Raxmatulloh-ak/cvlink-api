<?php

declare(strict_types=1);

namespace App\Component\CV;

use App\Component\Position\PositionEligibilityService;
use App\Entity\CV;
use App\Entity\User;
use App\Enum\CvStatus;

class CvAccessService
{
    public function __construct(
        private readonly PositionEligibilityService $positionEligibilityService,
    ) {
    }

    public function canRead(CV $cv, User $user): bool
    {
        if ($this->hasRole($user, 'ROLE_ADMIN')) {
            return true;
        }

        $candidate = $cv->getCandidate();
        $position = $cv->getPosition();

        if ($candidate === null || $position === null) {
            return false;
        }

        if (!$this->positionEligibilityService->isAllowed($position, $candidate)) {
            return false;
        }

        if ($candidate === $user && $this->hasRole($user, 'ROLE_CANDIDATE')) {
            return true;
        }

        return $cv->getStatus() === CvStatus::PUBLISHED
            && $this->hasRole($user, 'ROLE_RECRUITER');
    }

    public function canManage(CV $cv, User $user): bool
    {
        if ($this->hasRole($user, 'ROLE_ADMIN')) {
            return true;
        }

        if ($cv->getCandidate() !== $user || !$this->hasRole($user, 'ROLE_CANDIDATE')) {
            return false;
        }

        $position = $cv->getPosition();

        return $position !== null && $this->positionEligibilityService->isAllowed($position, $user);
    }

    private function hasRole(User $user, string $role): bool
    {
        return in_array($role, $user->getRoles(), true);
    }
}
