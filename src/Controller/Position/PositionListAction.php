<?php

declare(strict_types=1);

namespace App\Controller\Position;

use App\Component\Position\PositionEligibilityService;
use App\Entity\Position;
use App\Entity\User;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PositionListAction extends AbstractController
{
    private const PAGE_SIZE = 30;

    public function __construct(
        private readonly PositionRepository $positionRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $isRecruiter = $this->isGranted('ROLE_RECRUITER');
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        $positions = $this->positionRepository->findForBrowse(!$user instanceof User);

        if ($user instanceof User && !$isRecruiter && !$isAdmin) {
            $positions = $this->positionEligibilityService->filterAllowedPositions($positions, $user);
        }

        $page = max(1, $request->query->getInt('page', 1));
        $offset = ($page - 1) * self::PAGE_SIZE;
        $hasMore = count($positions) > $offset + self::PAGE_SIZE;
        $positions = array_slice($positions, $offset, self::PAGE_SIZE);

        return new JsonResponse([
            'items' => array_map(
                fn (Position $position): array => $this->positionItem($position),
                $positions,
            ),
            'page' => $page,
            'hasMore' => $hasMore,
        ]);
    }

    private function positionItem(Position $position): array
    {
        return [
            'id' => $position->getId(),
            'title' => $position->getTitle(),
            'description' => $position->getDescription(),
            'accessType' => $position->getAccessType()?->value,
            'maxProjects' => $position->getMaxProjects(),
            'version' => $position->getVersion(),
            'createdAt' => $position->getCreatedAt()?->format(DATE_ATOM),
            'updatedAt' => $position->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}
