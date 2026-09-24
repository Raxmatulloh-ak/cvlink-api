<?php

declare(strict_types=1);

namespace App\Component\PositionAccessRule;

use App\Component\Position\Dto\PositionAccessRuleDto;
use App\Entity\Position;
use App\Enum\PositionAccessType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PositionAccessRuleService
{
    public function __construct(
        private readonly PositionAccessRuleBuilder $positionAccessRuleBuilder,
        private readonly PositionAccessRuleFactory $positionAccessRuleFactory,
        private readonly PositionAccessRuleManager $positionAccessRuleManager,
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

        $this->clear($position);

        foreach ($rules as $ruleData) {
            $rule = $this->positionAccessRuleBuilder->build(
                $position,
                $ruleData,
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

    private function clear(Position $position): void
    {
        foreach ($position->getAccessRules()->toArray() as $rule) {
            $position->removeAccessRule($rule);
            $this->positionAccessRuleManager->remove($rule);
        }
    }
}
