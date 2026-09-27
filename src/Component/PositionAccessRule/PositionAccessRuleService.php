<?php

declare(strict_types=1);

namespace App\Component\PositionAccessRule;

use App\Component\Position\Dto\PositionAccessRuleDto;
use App\Entity\AttributeDefinition;
use App\Entity\AttributeOption;
use App\Entity\Position;
use App\Enum\PositionAccessType;
use App\Repository\AttributeDefinitionRepository;
use App\Repository\AttributeOptionRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PositionAccessRuleService
{
    public function __construct(
        private readonly PositionAccessRuleBuilder $positionAccessRuleBuilder,
        private readonly PositionAccessRuleFactory $positionAccessRuleFactory,
        private readonly PositionAccessRuleManager $positionAccessRuleManager,
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
        private readonly AttributeOptionRepository $attributeOptionRepository,
    ) {
    }

    /**
     * @param PositionAccessRuleDto[] $rules
     */
    public function replace(Position $position, array $rules): void
    {
        if ($position->getAccessType() === PositionAccessType::PUBLIC && $rules !== []) {
            throw new BadRequestHttpException('Public position cannot have access rules');
        }

        $attributes = $this->getAttributes($rules);
        $options = $this->getOptions($rules);

        $this->clear($position);

        foreach ($rules as $ruleData) {
            $attributeId = (int) $ruleData->getAttributeId();

            if (!isset($attributes[$attributeId])) {
                throw new BadRequestHttpException('Attribute not found');
            }

            $optionId = $ruleData->getOptionId();
            $option = $optionId === null ? null : ($options[$optionId] ?? null);

            $rule = $this->positionAccessRuleBuilder->build(
                $position,
                $ruleData,
                $attributes[$attributeId],
                $option,
            );

            $position->addAccessRule($rule);
            $this->positionAccessRuleManager->save($rule);
        }
    }

    public function copy(Position $source, Position $target): void
    {
        foreach ($source->getAccessRules() as $sourceRule) {
            $rule = $this->positionAccessRuleFactory->create(
                $target,
                $sourceRule->getAttribute(),
                $sourceRule->getOperation(),
                $sourceRule->getTextOperand(),
                $sourceRule->getNumericOperand(),
                $sourceRule->getDateOperand(),
                $sourceRule->getPeriodStartOperand(),
                $sourceRule->getPeriodEndOperand(),
                $sourceRule->isBooleanOperand(),
                $sourceRule->getOption(),
            );

            $target->addAccessRule($rule);
            $this->positionAccessRuleManager->save($rule);
        }
    }

    /**
     * @param PositionAccessRuleDto[] $rules
     *
     * @return array<int, AttributeDefinition>
     */
    private function getAttributes(array $rules): array
    {
        $ids = [];

        foreach ($rules as $rule) {
            $ids[(int) $rule->getAttributeId()] = true;
        }

        if ($ids === []) {
            return [];
        }

        $attributes = [];

        foreach ($this->attributeDefinitionRepository->findBy(['id' => array_keys($ids)]) as $attribute) {
            if ($attribute->getId() !== null) {
                $attributes[$attribute->getId()] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @param PositionAccessRuleDto[] $rules
     * @return array<int, AttributeOption>
     */
    private function getOptions(array $rules): array
    {
        $ids = [];

        foreach ($rules as $rule) {
            if ($rule->getOptionId() !== null) {
                $ids[$rule->getOptionId()] = true;
            }
        }

        if ($ids === []) {
            return [];
        }

        $options = [];

        foreach ($this->attributeOptionRepository->findBy(['id' => array_keys($ids)]) as $option) {
            if ($option->getId() !== null) {
                $options[$option->getId()] = $option;
            }
        }

        return $options;
    }

    private function clear(Position $position): void
    {
        foreach ($position->getAccessRules()->toArray() as $rule) {
            $position->removeAccessRule($rule);
            $this->positionAccessRuleManager->remove($rule);
        }
    }
}
