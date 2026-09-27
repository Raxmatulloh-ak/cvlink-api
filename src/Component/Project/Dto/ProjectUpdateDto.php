<?php

declare(strict_types=1);

namespace App\Component\Project\Dto;

class ProjectUpdateDto extends ProjectWriteDto
{
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
