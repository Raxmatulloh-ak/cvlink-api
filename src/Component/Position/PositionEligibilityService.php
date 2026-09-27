<?php

declare(strict_types=1);

namespace App\Component\Position;

use App\Entity\CV;
use App\Entity\Position;
use App\Entity\PositionAccessRule;
use App\Entity\User;
use App\Entity\UserAttributeValue;
use App\Enum\AccessRuleOperator;
use App\Enum\AttributeValueType;
use App\Enum\PositionAccessType;
use App\Repository\PositionAccessRuleRepository;
use App\Repository\UserAttributeValueRepository;

class PositionEligibilityService
{
    public function __construct(
        private readonly PositionAccessRuleRepository $positionAccessRuleRepository,
        private readonly UserAttributeValueRepository $userAttributeValueRepository,
    ) {
    }

    public function isAllowed(Position $position, User $candidate): bool
    {
        if ($position->getAccessType() === PositionAccessType::PUBLIC) {
            return true;
        }

        $rules = $this->positionAccessRuleRepository->findForPosition($position);

        if ($rules === []) {
            return false;
        }

        $values = [];

        foreach ($this->userAttributeValueRepository->findForOwner($candidate) as $value) {
            $attributeId = $value->getAttribute()?->getId();

            if ($attributeId !== null) {
                $values[$attributeId] = $value;
            }
        }

        return $this->matchesAll($rules, $values);
    }


    /**
     * @param Position[] $positions
     * @return Position[]
     */
    public function filterAllowedPositions(array $positions, User $candidate): array
    {
        if ($positions === []) {
            return [];
        }

        $rulesByPosition = [];

        foreach ($this->positionAccessRuleRepository->findForPositions($positions) as $rule) {
            $positionId = $rule->getPosition()?->getId();

            if ($positionId !== null) {
                $rulesByPosition[$positionId][] = $rule;
            }
        }

        $values = [];

        foreach ($this->userAttributeValueRepository->findForOwner($candidate) as $value) {
            $attributeId = $value->getAttribute()?->getId();

            if ($attributeId !== null) {
                $values[$attributeId] = $value;
            }
        }

        return array_values(array_filter($positions, function (Position $position) use ($rulesByPosition, $values): bool {
            if ($position->getAccessType() === PositionAccessType::PUBLIC) {
                return true;
            }

            $positionId = $position->getId();
            $rules = $positionId === null ? [] : ($rulesByPosition[$positionId] ?? []);

            if ($rules === []) {
                return false;
            }

            return $this->matchesAll($rules, $values);
        }));
    }

    /**
     * @param CV[] $cvs
     * @return CV[]
     */
    public function filterAllowedCvs(array $cvs): array
    {
        if ($cvs === []) {
            return [];
        }

        $positions = [];
        $candidates = [];

        foreach ($cvs as $cv) {
            $position = $cv->getPosition();
            $candidate = $cv->getCandidate();

            if ($position?->getId() !== null) {
                $positions[$position->getId()] = $position;
            }

            if ($candidate?->getId() !== null) {
                $candidates[$candidate->getId()] = $candidate;
            }
        }

        $rulesByPosition = [];

        foreach ($this->positionAccessRuleRepository->findForPositions(array_values($positions)) as $rule) {
            $positionId = $rule->getPosition()?->getId();

            if ($positionId !== null) {
                $rulesByPosition[$positionId][] = $rule;
            }
        }

        $valuesByCandidate = [];

        foreach ($this->userAttributeValueRepository->findForOwners(array_values($candidates)) as $value) {
            $candidateId = $value->getOwner()?->getId();
            $attributeId = $value->getAttribute()?->getId();

            if ($candidateId !== null && $attributeId !== null) {
                $valuesByCandidate[$candidateId][$attributeId] = $value;
            }
        }

        return array_values(array_filter($cvs, function (CV $cv) use ($rulesByPosition, $valuesByCandidate): bool {
            $position = $cv->getPosition();
            $candidate = $cv->getCandidate();
            $positionId = $position?->getId();
            $candidateId = $candidate?->getId();

            if ($position === null || $positionId === null || $candidateId === null) {
                return false;
            }

            if ($position->getAccessType() === PositionAccessType::PUBLIC) {
                return true;
            }

            $rules = $rulesByPosition[$positionId] ?? [];

            if ($rules === []) {
                return false;
            }

            return $this->matchesAll($rules, $valuesByCandidate[$candidateId] ?? []);
        }));
    }

    /**
     * @param PositionAccessRule[] $rules
     * @param array<int, UserAttributeValue> $values
     */
    private function matchesAll(array $rules, array $values): bool
    {
        foreach ($rules as $rule) {
            $attributeId = $rule->getAttribute()?->getId();
            $value = $attributeId === null ? null : ($values[$attributeId] ?? null);

            if ($value === null || !$this->matches($rule, $value)) {
                return false;
            }
        }

        return true;
    }

    private function matches(PositionAccessRule $rule, UserAttributeValue $value): bool
    {
        return match ($rule->getAttribute()?->getValueType()) {
            AttributeValueType::STRING,
            AttributeValueType::TEXT => $rule->getOperation() === AccessRuleOperator::EQUAL
                && $value->getTextValue() === $rule->getTextOperand(),

            AttributeValueType::NUMERIC => $this->compare(
                $value->getNumericValue(),
                $rule->getNumericOperand(),
                $rule->getOperation(),
            ),

            AttributeValueType::DATE => $this->compare(
                $value->getDateValue()?->format('Y-m-d'),
                $rule->getDateOperand()?->format('Y-m-d'),
                $rule->getOperation(),
            ),

            AttributeValueType::PERIOD => $this->compare(
                    $value->getPeriodStart()?->format('Y-m-d'),
                    $rule->getPeriodStartOperand()?->format('Y-m-d'),
                    $rule->getOperation(),
                ) && $this->compare(
                    $value->getPeriodEnd()?->format('Y-m-d'),
                    $rule->getPeriodEndOperand()?->format('Y-m-d'),
                    $rule->getOperation(),
                ),

            AttributeValueType::BOOLEAN => $rule->getOperation() === AccessRuleOperator::EQUAL
                && $value->isBooleanValue() !== null
                && $value->isBooleanValue() === $rule->isBooleanOperand(),

            AttributeValueType::DROPDOWN => $rule->getOperation() === AccessRuleOperator::EQUAL
                && $value->getOption()?->getId() !== null
                && $value->getOption()?->getId() === $rule->getOption()?->getId(),

            default => false,
        };
    }

    private function compare(int|float|string|null $left, int|float|string|null $right, ?AccessRuleOperator $operator): bool
    {
        if ($left === null || $right === null || $operator === null) {
            return false;
        }

        return match ($operator) {
            AccessRuleOperator::EQUAL => $left === $right,
            AccessRuleOperator::GREATER_THAN => $left > $right,
            AccessRuleOperator::GREATER_THAN_OR_EQUAL => $left >= $right,
            AccessRuleOperator::LESS_THAN => $left < $right,
            AccessRuleOperator::LESS_THAN_OR_EQUAL => $left <= $right,
        };
    }
}
