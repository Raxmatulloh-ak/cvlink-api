<?php

declare(strict_types=1);

namespace App\Component\PositionAttribute;

use App\Entity\AttributeDefinition;
use App\Entity\Position;
use App\Entity\PositionAttribute;

class PositionAttributeFactory
{
    public function create(
        Position $position,
        AttributeDefinition $attribute,
        int $displayOrder,
    ): PositionAttribute {
        return new PositionAttribute()
            ->setPosition($position)
            ->setAttribute($attribute)
            ->setDisplayOrder($displayOrder);
    }
}
