<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Entity\User;
use App\Repository\UserAttributeValueRepository;

class UserDisplayNameProvider
{
    public function __construct(
        private readonly UserAttributeValueRepository $userAttributeValueRepository
    ) {
    }

    /**
     * @param User[] $users
     *
     * @return array<int, string>
     */
    public function getForUsers(array $users): array
    {
        if ($users === []) {
            return [];
        }

        $parts = [];

        foreach ($this->userAttributeValueRepository->findNameValuesForOwners($users) as $value) {
            $ownerId = $value->getOwner()?->getId();
            $builtinKey = $value->getAttribute()?->getBuiltinKey();

            if ($ownerId !== null && $builtinKey !== null) {
                $parts[$ownerId][$builtinKey] = $value->getTextValue();
            }
        }

        $names = [];

        foreach ($users as $user) {
            $userId = $user->getId();

            if ($userId === null) {
                continue;
            }

            $name = trim(sprintf(
                '%s %s',
                $parts[$userId]['first_name'] ?? '',
                $parts[$userId]['last_name'] ?? '',
            ));

            $names[$userId] = $name !== '' ? $name : 'User ' . $userId;
        }

        return $names;
    }
}
