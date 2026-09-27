<?php

declare(strict_types=1);

namespace App\Component\CV\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CvCreateDto
{
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $positionId = null;

    #[Assert\Positive]
    private ?int $candidateId = null;

    public function getPositionId(): ?int
    {
        return $this->positionId;
    }

    public function setPositionId(?int $positionId): static
    {
        $this->positionId = $positionId;

        return $this;
    }

    public function getCandidateId(): ?int
    {
        return $this->candidateId;
    }

    public function setCandidateId(?int $candidateId): static
    {
        $this->candidateId = $candidateId;

        return $this;
    }
}
