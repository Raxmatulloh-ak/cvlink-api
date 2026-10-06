<?php

declare(strict_types=1);

namespace App\Component\Salesforce\Dto;

class SalesforceUserDataDto
{
    public function __construct(
        private readonly string $email,
        private readonly ?string $firstName,
        private readonly ?string $lastName,
        private readonly ?string $location,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }
}
