<?php

declare(strict_types=1);

namespace App\Controller\Position;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\Position\PositionFactory;
use App\Component\Position\PositionManager;
use App\Controller\Base\AbstractController;
use App\Entity\Position;

class PositionCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly PositionFactory $positionFactory,
        private readonly PositionManager $positionManager,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(Position $data): Position
    {
        $this->validate($data);
        $title = trim((string)$data->getTitle());
        $accessType = $data->getAccessType();

        $position = $this->positionFactory->create(
            $title,
            $data->getDescription(),
            $accessType,
            $data->getMaxProjects(),
        );

        $this->positionManager->save($position, true);

        return $position;
    }
}
