<?php

declare(strict_types=1);

namespace App\Component\AttributeDefinition;

use App\Entity\AttributeCategory;
use App\Entity\AttributeDefinition;
use App\Enum\AttributeValueType;

class AttributeDefinitionFactory
{
    public function create(
        AttributeCategory $category,
        string $name,
        ?string $description,
        AttributeValueType $valueType,
        ?string $builtinKey = null
    ): AttributeDefinition {
        return new AttributeDefinition()
            ->setCategory($category)
            ->setName($name)
            ->setDescription($description)
            ->setValueType($valueType)
            ->setBuiltinKey($builtinKey);
    }
}
