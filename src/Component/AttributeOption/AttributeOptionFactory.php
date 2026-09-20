<?php

declare(strict_types=1);

namespace App\Component\AttributeOption;

use App\Entity\AttributeDefinition;
use App\Entity\AttributeOption;

class AttributeOptionFactory
{
    public function create(
        AttributeDefinition $attribute,
        string $label,
        int $displayOrder,
    ): AttributeOption {
        return new AttributeOption()
            ->setAttribute($attribute)
            ->setLabel($label)
            ->setDisplayOrder($displayOrder);
    }
}
