<?php

declare(strict_types=1);

namespace App\Component\UserAuthIdentity;

use App\Entity\User;
use App\Entity\UserAuthIdentity;

class UserAuthIdentityFactory
{
    public function create(
        User $owner,
        string $provider,
        string $providerSubject,
    ): UserAuthIdentity {
        return new UserAuthIdentity()
            ->setOwner($owner)
            ->setProvider($provider)
            ->setProviderSubject($providerSubject);
    }
}
