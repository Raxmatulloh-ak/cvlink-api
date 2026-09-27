<?php

declare(strict_types=1);

namespace App\Controller\Discovery;

use App\Component\Position\PositionEligibilityService;
use App\Component\User\UserDisplayNameProvider;
use App\Entity\Position;
use App\Entity\User;
use App\Repository\CVRepository;
use App\Repository\CvLikeRepository;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchAction extends AbstractController
{
    private const RESULT_LIMIT = 30;

    public function __construct(
        private readonly PositionRepository $positionRepository,
        private readonly CVRepository $cvRepository,
        private readonly CvLikeRepository $cvLikeRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
        private readonly UserDisplayNameProvider $userDisplayNameProvider,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string)$request->query->get('q', ''));

        if ($query === '') {
            return new JsonResponse([
                'positions' => [],
                'cvs' => [],
            ]);
        }

        $user = $this->getUser();
        $isRecruiter = $this->isGranted('ROLE_RECRUITER');
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $publicOnly = !$user instanceof User;

        $positionIds = $this->positionRepository->searchIds($query, $publicOnly, self::RESULT_LIMIT * 2);
        $positions = $this->positionRepository->findByIds($positionIds);

        if ($user instanceof User && !$isRecruiter && !$isAdmin) {
            $positions = $this->positionEligibilityService->filterAllowedPositions($positions, $user);
        }

        $positions = array_slice($positions, 0, self::RESULT_LIMIT);
        $positionItems = array_map(
            static fn(Position $position): array => [
                'id' => $position->getId(),
                'title' => $position->getTitle(),
                'description' => $position->getDescription(),
            ],
            $positions,
        );

        $cvItems = [];

        if ($user instanceof User) {
            $cvIds = $this->cvRepository->searchIds(
                $query,
                $user,
                $isAdmin,
                $isRecruiter,
                self::RESULT_LIMIT * 2,
            );
            $cvs = $this->cvRepository->findByIds($cvIds);

            if (!$isAdmin) {
                $cvs = $this->positionEligibilityService->filterAllowedCvs($cvs);
            }

            $cvs = array_slice($cvs, 0, self::RESULT_LIMIT);
            $likes = $this->cvLikeRepository->countForCvs($cvs);
            $candidates = [];

            foreach ($cvs as $cv) {
                $candidate = $cv->getCandidate();

                if ($candidate?->getId() !== null) {
                    $candidates[$candidate->getId()] = $candidate;
                }
            }

            $candidateNames = $this->userDisplayNameProvider->getForUsers(array_values($candidates));

            foreach ($cvs as $cv) {
                $candidate = $cv->getCandidate();
                $position = $cv->getPosition();
                $cvId = $cv->getId();
                $candidateId = $candidate?->getId();

                if ($cvId === null || $candidateId === null || $position === null) {
                    continue;
                }

                $cvItems[] = [
                    'id' => $cvId,
                    'candidateId' => $candidateId,
                    'candidateName' => $candidateNames[$candidateId] ?? null,
                    'positionId' => $position->getId(),
                    'positionTitle' => $position->getTitle(),
                    'status' => $cv->getStatus()?->value,
                    'likes' => $likes[$cvId] ?? 0,
                ];
            }
        }

        return new JsonResponse(['positions' => $positionItems, 'cvs' => $cvItems]);
    }
}
