<?php

declare(strict_types=1);

namespace App\Controller\Position;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\Position\Dto\PositionUpdateDto;
use App\Component\Position\PositionConfigurationService;
use App\Component\Position\PositionManager;
use App\Controller\Base\AbstractController;
use App\Entity\Position;
use App\Entity\User;
use App\Repository\PositionRepository;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PositionUpdateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly PositionRepository $positionRepository,
        private readonly PositionManager $positionManager,
        private readonly PositionConfigurationService $positionConfigurationService,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(PositionUpdateDto $data, Request $request): Position
    {
        $this->validate($data);
        $position = $this->positionRepository->find($request->attributes->getInt('id'));

        if ($position === null) {
            throw new NotFoundHttpException('Position not found');
        }

        if ($position->getVersion() !== $data->getVersion()) {
            throw new ConflictHttpException('Position was modified by another user');
        }

        $position
            ->setTitle($data->getTitle())
            ->setDescription($data->getDescription())
            ->setAccessType($data->getAccessType())
            ->setMaxProjects($data->getMaxProjects());

        $user = $this->getUser();

        if ($user instanceof User) {
            $position->setUpdatedBy($user);
        }

        $this->positionConfigurationService->replace($position, $data);

        try {
            $this->positionManager->save($position, true);
        } catch (OptimisticLockException $exception) {
            throw new ConflictHttpException('Position was modified by another user', $exception);
        }

        return $position;
    }
}
