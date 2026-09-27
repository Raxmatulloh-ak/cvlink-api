<?php

declare(strict_types=1);

namespace App\Controller\Position;

use App\Component\Position\PositionEligibilityService;
use App\Entity\User;
use App\Enum\PositionAccessType;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class PositionReadAction extends AbstractController
{
    public function __construct(
        private readonly PositionRepository $positionRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $position = $this->positionRepository->find($id);

        if ($position === null) {
            throw new NotFoundHttpException('Position not found');
        }

        $user = $this->getUser();

        if (!$user instanceof User && $position->getAccessType() !== PositionAccessType::PUBLIC) {
            throw new NotFoundHttpException('Position not found');
        }

        if (
            $user instanceof User
            && !$this->isGranted('ROLE_RECRUITER')
            && !$this->isGranted('ROLE_ADMIN')
            && !$this->positionEligibilityService->isAllowed($position, $user)
        ) {
            throw new NotFoundHttpException('Position not found');
        }

        $json = $this->serializer->serialize($position, 'json', [
            'groups' => ['position:read'],
        ]);

        return new JsonResponse($json);
    }
}
