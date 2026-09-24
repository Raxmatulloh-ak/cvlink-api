<?php

declare(strict_types=1);

namespace App\Component\AttributeDefinition;

use App\Entity\AttributeDefinition;
use App\Repository\AttributeDefinitionRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AttributeDefinitionResolver
{
    public function __construct(
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
    ) {
    }

    public function get(int $id): AttributeDefinition
    {
        $attributeDefinition = $this->attributeDefinitionRepository->find($id);

        if ($attributeDefinition === null) {
            throw new BadRequestHttpException('Attribute not found');
        }

        return $attributeDefinition;
    }
}
