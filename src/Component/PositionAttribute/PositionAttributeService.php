<?php

declare(strict_types=1);

namespace App\Component\PositionAttribute;

use App\Component\Position\Dto\PositionAttributeDto;
use App\Entity\AttributeDefinition;
use App\Entity\Position;
use App\Entity\PositionAttribute;
use App\Repository\AttributeDefinitionRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PositionAttributeService
{
    public function __construct(
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
        private readonly PositionAttributeFactory $positionAttributeFactory,
        private readonly PositionAttributeManager $positionAttributeManager,
    ) {
    }

    /**
     * @param PositionAttributeDto[] $items
     */
    public function replace(Position $position, array $items): void
    {
        $existing = $this->getExisting($position);
        $received = [];
        $attributeIds = [];

        foreach ($items as $item) {
            $attributeId = (int)$item->getAttributeId();

            if (isset($received[$attributeId])) {
                throw new BadRequestHttpException('Attribute is duplicated in position');
            }

            $received[$attributeId] = true;
            $attributeIds[] = $attributeId;
        }

        $attributes = $this->getAttributes($attributeIds);

        foreach ($items as $item) {
            $attributeId = (int)$item->getAttributeId();

            if (isset($existing[$attributeId])) {
                $existing[$attributeId]->setDisplayOrder((int)$item->getDisplayOrder());
                $this->positionAttributeManager->save($existing[$attributeId]);
                unset($existing[$attributeId]);

                continue;
            }

            if (!isset($attributes[$attributeId])) {
                throw new BadRequestHttpException('Attribute not found');
            }

            $positionAttribute = $this->positionAttributeFactory->create(
                $position,
                $attributes[$attributeId],
                $item->getDisplayOrder(),
            );

            $position->addPositionAttribute($positionAttribute);
            $this->positionAttributeManager->save($positionAttribute);
        }

        foreach ($existing as $positionAttribute) {
            $position->removePositionAttribute($positionAttribute);
            $this->positionAttributeManager->remove($positionAttribute);
        }
    }

    public function copy(Position $source, Position $target): void
    {
        foreach ($source->getPositionAttributes() as $sourceAttribute) {
            $positionAttribute = $this->positionAttributeFactory->create(
                $target,
                $sourceAttribute->getAttribute(),
                $sourceAttribute->getDisplayOrder(),
            );

            $target->addPositionAttribute($positionAttribute);
            $this->positionAttributeManager->save($positionAttribute);
        }
    }

    /**
     * @param int[] $ids
     * @return array<int, AttributeDefinition>
     */
    private function getAttributes(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $attributes = [];

        foreach ($this->attributeDefinitionRepository->findBy(['id' => $ids]) as $attribute) {
            if ($attribute->getId() !== null) {
                $attributes[$attribute->getId()] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @return array<int, PositionAttribute>
     */
    private function getExisting(Position $position): array
    {
        $existing = [];

        foreach ($position->getPositionAttributes() as $positionAttribute) {
            $attributeId = $positionAttribute->getAttribute()?->getId();

            if ($attributeId !== null) {
                $existing[$attributeId] = $positionAttribute;
            }
        }

        return $existing;
    }
}
