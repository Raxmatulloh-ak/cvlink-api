<?php

declare(strict_types=1);

namespace App\Controller\Position;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\Position\Dto\PositionWriteDto;
use App\Component\Position\PositionConfigurationService;
use App\Component\Position\PositionFactory;
use App\Component\Position\PositionManager;
use App\Controller\Base\AbstractController;
use App\Entity\Position;
use App\Entity\User;

class PositionCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly PositionFactory $positionFactory,
        private readonly PositionManager $positionManager,
        private readonly PositionConfigurationService $positionConfigurationService,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(PositionWriteDto $data): Position
    {
        $this->validate($data);

        $position = $this->positionFactory->create(
            $data->getTitle(),
            $data->getDescription(),
            $data->getAccessType(),
            $data->getMaxProjects(),
        );

        $this->positionManager->save($position);
        $this->positionConfigurationService->replace($position, $data);
        $this->positionManager->flush();

        return $position;
    }
}
