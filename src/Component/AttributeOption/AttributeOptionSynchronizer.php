<?php

declare(strict_types=1);

namespace App\Component\AttributeOption;

use App\Entity\AttributeDefinition;
use App\Enum\AttributeValueType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AttributeOptionSynchronizer
{
    public function __construct(
        private readonly AttributeOptionFactory $attributeOptionFactory,
        private readonly AttributeOptionManager $attributeOptionManager,
    ) {
    }

    public function sync(AttributeDefinition $attributeDefinition, array $options): void
    {
        $this->validate($attributeDefinition->getValueType(), $options);
        $existingOptions = [];

        foreach ($attributeDefinition->getOptions() as $option) {
            if ($option->getId() !== null) {
                $existingOptions[$option->getId()] = $option;
            }
        }

        $receivedIds = [];

        foreach ($options as $optionData) {
            $id = $optionData['id'] ?? null;

            if ($id === null) {
                $option = $this->attributeOptionFactory->create(
                    $attributeDefinition,
                    $optionData['label'],
                    $optionData['displayOrder'],
                );

                $attributeDefinition->addOption($option);
                $this->attributeOptionManager->save($option);

                continue;
            }

            if (!isset($existingOptions[$id])) {
                throw new BadRequestHttpException('Attribute option not found');
            }

            $option = $existingOptions[$id];

            $option
                ->setLabel(trim($optionData['label']))
                ->setDisplayOrder($optionData['displayOrder']);

            $this->attributeOptionManager->save($option);
            $receivedIds[$id] = true;
        }

        foreach ($existingOptions as $id => $option) {
            if (isset($receivedIds[$id])) {
                continue;
            }

            $attributeDefinition->removeOption($option);
            $this->attributeOptionManager->remove($option);
        }
    }

    private function validate(?AttributeValueType $valueType, array $options): void
    {
        if ($valueType !== AttributeValueType::DROPDOWN && $options !== []) {
            throw new BadRequestHttpException('Options are allowed only for dropdown attributes');
        }

        if ($valueType === AttributeValueType::DROPDOWN && $options === []) {
            throw new BadRequestHttpException('Dropdown attribute must have at least one option');
        }
    }
}
