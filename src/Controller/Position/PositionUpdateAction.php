<?php

declare(strict_types=1);

namespace App\Controller\Position;

use App\Component\Position\Dto\PositionUpdateDto;
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
    public function __invoke(
        PositionUpdateDto $data,
        Request $request,
        PositionRepository $positionRepository,
        PositionManager $positionManager,
    ): Position {
        $this->validate($data);
        $position = $positionRepository->find($request->attributes->getInt('id'));

        if ($position === null) {
            throw new NotFoundHttpException('Position not found');
        }

        if ($position->getVersion() !== $data->getVersion()) {
            throw new ConflictHttpException('Position was modified by another user');
        }

        $title = trim((string)$data->getTitle());
        $accessType = $data->getAccessType();
        $maxProjects = $data->getMaxProjects();

        $position
            ->setTitle($title)
            ->setDescription($data->getDescription())
            ->setAccessType($accessType)
            ->setMaxProjects($maxProjects);

        $currentUser = $this->getUser();

        if ($currentUser instanceof User) {
            $position->setUpdatedBy($currentUser);
        }

        try {
            $positionManager->save($position, true);
        } catch (OptimisticLockException $exception) {
            throw new ConflictHttpException('Position was modified by another user', $exception);
        }

        return $position;
    }
}
