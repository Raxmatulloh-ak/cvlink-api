<?php

declare(strict_types=1);

namespace App\Component\User\Dto;
use Symfony\Component\Serializer\Attribute\Groups;

use App\Enum\Theme;
use Symfony\Component\Validator\Constraints as Assert;

class UserPreferencesDto
{
    #[Assert\Length(min: 2, max: 15)]
    #[Groups(['user:write'])]
    private ?string $locale = null;

    #[Groups(['user:write'])]
    private ?Theme $theme = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }
}
