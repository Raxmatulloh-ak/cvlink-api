<?php

declare(strict_types=1);

namespace App\Component\Position;

use App\Component\Position\Dto\PositionWriteDto;
use App\Component\PositionAccessRule\PositionAccessRuleService;
use App\Component\PositionAttribute\PositionAttributeService;
use App\Component\PositionTag\PositionTagService;
use App\Entity\Position;

class PositionConfigurationService
{
    public function __construct(
        private readonly PositionAttributeService $positionAttributeService,
        private readonly PositionAccessRuleService $positionAccessRuleService,
        private readonly PositionTagService $positionTagService,
    ) {
    }

    public function replace(Position $position, PositionWriteDto $data): void
    {
        $this->positionAttributeService->replace($position, $data->getAttributes());
        $this->positionAccessRuleService->replace($position, $data->getAccessRules());
        $this->positionTagService->replace($position, $data->getTags());
    }

    public function copy(Position $source, Position $target): void
    {
        $this->positionAttributeService->copy($source, $target);
        $this->positionAccessRuleService->copy($source, $target);
        $this->positionTagService->copy($source, $target);
    }
}
