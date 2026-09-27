<?php

declare(strict_types=1);

namespace App\Controller\AttributeDefinition;

use App\Repository\AttributeDefinitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AttributeLookupAction extends AbstractController
{
    public function __construct(
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $prefix = trim((string) $request->query->get('prefix', ''));
        $categoryId = $request->query->getInt('category') ?: null;
        $attributes = $this->attributeDefinitionRepository->findForLookup(
            $prefix === '' ? null : $prefix,
            $categoryId,
        );

        $items = [];

        foreach ($attributes as $attribute) {
            $items[] = [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'description' => $attribute->getDescription(),
                'valueType' => $attribute->getValueType()?->value,
                'category' => [
                    'id' => $attribute->getCategory()?->getId(),
                    'name' => $attribute->getCategory()?->getName(),
                ],
            ];
        }

        return new JsonResponse(['items' => $items]);
    }
}
