<?php

declare(strict_types=1);

namespace App\Component\PositionAccessRule;

use App\Component\AttributeDefinition\AttributeDefinitionResolver;
use App\Component\Position\Dto\PositionAccessRuleDto;
use App\Entity\AttributeDefinition;
use App\Entity\AttributeOption;
use App\Entity\Position;
use App\Entity\PositionAccessRule;
use App\Enum\AccessRuleOperator;
use App\Enum\AttributeValueType;
use App\Repository\AttributeOptionRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PositionAccessRuleBuilder
{
    public function __construct(
        private readonly AttributeDefinitionResolver $attributeDefinitionResolver,
        private readonly AttributeOptionRepository $attributeOptionRepository,
        private readonly PositionAccessRuleFactory $positionAccessRuleFactory,
    ) {
    }

    public function build(Position $position, PositionAccessRuleDto $data): PositionAccessRule
    {
        $attribute = $this->attributeDefinitionResolver->get((int)$data->getAttributeId());
        $operation = $data->getOperation();

        if ($operation === null) {
            throw new BadRequestHttpException('Access rule operation is required');
        }

        $this->validateOperation($attribute, $operation);

        return match ($attribute->getValueType()) {
            AttributeValueType::STRING,
            AttributeValueType::TEXT => $this->positionAccessRuleFactory->create(
                $position,
                $attribute,
                $operation,
                textOperand: $this->getTextOperand($data),
            ),

            AttributeValueType::NUMERIC => $this->positionAccessRuleFactory->create(
                $position,
                $attribute,
                $operation,
                numericOperand: $this->getNumericOperand($data),
            ),

            AttributeValueType::DATE => $this->positionAccessRuleFactory->create(
                $position,
                $attribute,
                $operation,
                dateOperand: $this->getDateOperand($data),
            ),

            AttributeValueType::PERIOD => $this->positionAccessRuleFactory->create(
                $position,
                $attribute,
                $operation,
                periodStartOperand: $this->getPeriodStart($data),
                periodEndOperand: $this->getPeriodEnd($data),
            ),

            AttributeValueType::BOOLEAN => $this->positionAccessRuleFactory->create(
                $position,
                $attribute,
                $operation,
                booleanOperand: $this->getBooleanOperand($data),
            ),

            AttributeValueType::DROPDOWN => $this->positionAccessRuleFactory->create(
                $position,
                $attribute,
                $operation,
                option: $this->getOption(
                    $attribute,
                    $data->getOptionId(),
                ),
            ),

            default => throw new BadRequestHttpException('Attribute type cannot be used in access rule'),
        };
    }

    private function validateOperation(AttributeDefinition $attribute, AccessRuleOperator $operation): void
    {
        if (
            in_array(
                $attribute->getValueType(),
                [
                    AttributeValueType::STRING,
                    AttributeValueType::TEXT,
                    AttributeValueType::BOOLEAN,
                    AttributeValueType::DROPDOWN,
                ],
                true,
            )
            && $operation !== AccessRuleOperator::EQUAL
        ) {
            throw new BadRequestHttpException('This attribute supports only equal operator');
        }
    }

    private function getOption(AttributeDefinition $attribute, ?int $optionId): AttributeOption
    {
        if ($optionId === null) {
            throw new BadRequestHttpException('Option is required');
        }

        $option = $this->attributeOptionRepository->find($optionId);

        if ($option === null || $option->getAttribute()?->getId() !== $attribute->getId()) {
            throw new BadRequestHttpException('Invalid attribute option');
        }

        return $option;
    }

    private function getTextOperand(PositionAccessRuleDto $data): string
    {
        $value = trim((string)$data->getTextOperand());

        if ($value === '') {
            throw new BadRequestHttpException('Text operand is required');
        }

        return $value;
    }

    private function getNumericOperand(PositionAccessRuleDto $data): float
    {
        return $data->getNumericOperand() ?? throw new BadRequestHttpException('Numeric operand is required');
    }

    private function getDateOperand(PositionAccessRuleDto $data): \DateTimeImmutable
    {
        return $data->getDateOperand() ?? throw new BadRequestHttpException('Date operand is required');
    }

    private function getPeriodStart(PositionAccessRuleDto $data): \DateTimeImmutable
    {
        return $data->getPeriodStartOperand() ?? throw new BadRequestHttpException('Period start is required');
    }

    private function getPeriodEnd(PositionAccessRuleDto $data): \DateTimeImmutable
    {
        $start = $this->getPeriodStart($data);

        $end = $data->getPeriodEndOperand() ?? throw new BadRequestHttpException('Period end is required');

        if ($start > $end) {
            throw new BadRequestHttpException('Period start cannot be after period end');
        }

        return $end;
    }

    private function getBooleanOperand(PositionAccessRuleDto $data): bool
    {
        return $data->getBooleanOperand() ?? throw new BadRequestHttpException('Boolean operand is required');
    }
}
