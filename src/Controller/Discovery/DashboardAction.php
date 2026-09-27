<?php

declare(strict_types=1);

namespace App\Controller\Discovery;

use App\Component\Position\PositionEligibilityService;
use App\Entity\Position;
use App\Entity\User;
use App\Repository\CVRepository;
use App\Repository\PositionRepository;
use App\Repository\PositionTagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class DashboardAction extends AbstractController
{
    public function __construct(
        private readonly PositionRepository $positionRepository,
        private readonly PositionTagRepository $positionTagRepository,
        private readonly CVRepository $cvRepository,
        private readonly UserRepository $userRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();
        $isRecruiter = $this->isGranted('ROLE_RECRUITER');
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $publicOnly = !$user instanceof User;

        $latest = $this->positionRepository->findLatestForDashboard($publicOnly);
        $popular = $this->positionRepository->findPopularForDashboard($publicOnly);
        $tagPositions = $this->positionRepository->findForTagCloud($publicOnly);

        $positions = [];

        foreach ($latest as $position) {
            $positions[$position->getId()] = $position;
        }

        foreach ($popular as $row) {
            $position = $row[0];
            $positions[$position->getId()] = $position;
        }

        foreach ($tagPositions as $position) {
            $positions[$position->getId()] = $position;
        }

        if ($user instanceof User && !$isRecruiter && !$isAdmin) {
            $positions = $this->toPositionMap(
                $this->positionEligibilityService->filterAllowedPositions(array_values($positions), $user),
            );
        }

        $latestItems = [];

        foreach ($latest as $position) {
            if (!isset($positions[$position->getId()])) {
                continue;
            }

            $latestItems[] = $this->positionItem($position);

            if (count($latestItems) === 10) {
                break;
            }
        }

        $popularItems = [];

        foreach ($popular as $row) {
            $position = $row[0];

            if (!isset($positions[$position->getId()])) {
                continue;
            }

            $popularItems[] = [
                ...$this->positionItem($position),
                'submittedCvs' => (int) $row['submittedCvs'],
            ];

            if (count($popularItems) === 5) {
                break;
            }
        }

        $availableTagPositions = array_values(array_filter(
            $tagPositions,
            static fn (Position $position): bool => isset($positions[$position->getId()]),
        ));

        return new JsonResponse([
            'latestPositions' => $latestItems,
            'popularPositions' => $popularItems,
            'tags' => [
                'target' => $isRecruiter || $isAdmin ? 'cvs' : 'positions',
                'items' => $this->positionTagRepository->countForPositions($availableTagPositions),
            ],
            'statistics' => [
                'cvsLast24Hours' => $this->cvRepository->countCreatedSince(new \DateTimeImmutable('-24 hours')),
                'totalPositions' => $this->positionRepository->countAll(),
                'totalCandidates' => $this->userRepository->countByRole('ROLE_CANDIDATE'),
                'totalRecruiters' => $this->userRepository->countByRole('ROLE_RECRUITER'),
                'submittedCvs' => $this->cvRepository->countPublished(),
            ],
        ]);
    }

    /**
     * @param Position[] $positions
     * @return array<int, Position>
     */
    private function toPositionMap(array $positions): array
    {
        $result = [];

        foreach ($positions as $position) {
            if ($position->getId() !== null) {
                $result[$position->getId()] = $position;
            }
        }

        return $result;
    }

    private function positionItem(Position $position): array
    {
        $changedAt = $position->getUpdatedAt() ?? $position->getCreatedAt();

        return [
            'id' => $position->getId(),
            'title' => $position->getTitle(),
            'description' => $position->getDescription(),
            'changedAt' => $changedAt?->format(DATE_ATOM),
        ];
    }
}
