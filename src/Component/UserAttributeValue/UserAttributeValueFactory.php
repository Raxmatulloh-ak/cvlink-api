<?php

declare(strict_types=1);

namespace App\Component\UserAttributeValue;

use App\Entity\AttributeDefinition;
use App\Entity\User;
use App\Entity\UserAttributeValue;

class UserAttributeValueFactory
{
    public function create(
        User $user,
        AttributeDefinition $attribute,
    ): UserAttributeValue {
        return new UserAttributeValue()
            ->setOwner($user)
            ->setAttribute($attribute);
    }
}
