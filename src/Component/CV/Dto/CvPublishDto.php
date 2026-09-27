<?php

declare(strict_types=1);

namespace App\Component\CV\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CvPublishDto
{
    #[Assert\Positive]
    private int $version = 0;

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }
}
