<?php

declare(strict_types=1);

namespace App\Component\PositionAccessRule;

use App\Entity\AttributeDefinition;
use App\Entity\AttributeOption;
use App\Entity\Position;
use App\Entity\PositionAccessRule;
use App\Enum\AccessRuleOperator;
use DateTimeImmutable;

class PositionAccessRuleFactory
{
    public function create(
        Position $position,
        AttributeDefinition $attribute,
        AccessRuleOperator $operation,
        ?string $textOperand = null,
        ?float $numericOperand = null,
        ?DateTimeImmutable $dateOperand = null,
        ?DateTimeImmutable $periodStartOperand = null,
        ?DateTimeImmutable $periodEndOperand = null,
        ?bool $booleanOperand = null,
        ?AttributeOption $option = null,
    ): PositionAccessRule {
        return new PositionAccessRule()
            ->setPosition($position)
            ->setAttribute($attribute)
            ->setOperation($operation)
            ->setTextOperand($textOperand)
            ->setNumericOperand($numericOperand)
            ->setDateOperand($dateOperand)
            ->setPeriodStartOperand($periodStartOperand)
            ->setPeriodEndOperand($periodEndOperand)
            ->setBooleanOperand($booleanOperand)
            ->setOption($option);
    }
}
