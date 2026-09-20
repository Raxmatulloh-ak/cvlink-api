<?php

declare(strict_types=1);

namespace App\Component\AttributeCategory;

use App\Entity\AttributeCategory;

class AttributeCategoryFactory
{
    public function create(
        string $name,
        int $displayOrder,
    ): AttributeCategory {
        return new AttributeCategory()
            ->setName($name)
            ->setDisplayOrder($displayOrder);
    }
}
