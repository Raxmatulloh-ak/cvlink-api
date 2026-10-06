<?php

declare(strict_types=1);

namespace App\Component\Salesforce\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SalesforceCreateDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['user:write'])]
    private ?string $companyName = null;

    #[Assert\Length(max: 128)]
    #[Groups(['user:write'])]
    private ?string $jobTitle = null;

    #[Assert\Length(max: 40)]
    #[Groups(['user:write'])]
    private ?string $phone = null;

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName === null ? null : trim($companyName);

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): static
    {
        $this->jobTitle = $this->normalizeNullableString($jobTitle);

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $this->normalizeNullableString($phone);

        return $this;
    }

    private function normalizeNullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
