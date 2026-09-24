<?php

declare(strict_types=1);

namespace App\Component\Position;

use App\Entity\Position;
use App\Entity\User;

class PositionDuplicator
{
    public function __construct(
        private readonly PositionFactory $positionFactory,
        private readonly PositionManager $positionManager,
        private readonly PositionConfigurationService $positionConfigurationService,
    ) {
    }

    public function duplicate(Position $source, ?User $updatedBy): Position
    {
        $position = $this->positionFactory->create(
            $source->getTitle() . ' Copy',
            $source->getDescription(),
            $source->getAccessType(),
            $source->getMaxProjects(),
        );

        $position->setUpdatedBy($updatedBy);

        $this->positionManager->save($position);
        $this->positionConfigurationService->copy($source, $position);
        $this->positionManager->flush();

        return $position;
    }
}
