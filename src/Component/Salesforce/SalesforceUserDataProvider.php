<?php

declare(strict_types=1);

namespace App\Component\Salesforce;

use App\Component\Salesforce\Dto\SalesforceUserDataDto;
use App\Entity\User;
use App\Repository\UserAttributeValueRepository;

class SalesforceUserDataProvider
{
    public function __construct(
        private readonly UserAttributeValueRepository $userAttributeValueRepository,
    ) {
    }

    public function get(User $user): SalesforceUserDataDto
    {
        $values = $this->userAttributeValueRepository->findSalesforceValuesForOwner($user);

        return new SalesforceUserDataDto(
            (string) $user->getEmail(),
            $values['first_name'] ?? null,
            $values['last_name'] ?? null,
            $values['location'] ?? null,
        );
    }
}
