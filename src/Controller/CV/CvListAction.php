<?php

declare(strict_types=1);

namespace App\Controller\CV;

use App\Component\Position\PositionEligibilityService;
use App\Component\User\UserDisplayNameProvider;
use App\Entity\User;
use App\Repository\CVRepository;
use App\Repository\CvLikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CvListAction extends AbstractController
{
    private const PAGE_SIZE = 30;

    public function __construct(
        private readonly CVRepository $cvRepository,
        private readonly CvLikeRepository $cvLikeRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
        private readonly UserDisplayNameProvider $userDisplayNameProvider,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $page = max(1, $request->query->getInt('page', 1));
        $positionId = $request->query->getInt('position') ?: null;
        $candidateId = $request->query->getInt('candidate') ?: null;

        $cvs = $this->cvRepository->findForList(
            $user,
            $this->isGranted('ROLE_ADMIN'),
            $this->isGranted('ROLE_RECRUITER'),
            $positionId,
            $candidateId,
            $page,
            self::PAGE_SIZE,
        );

        $hasMore = count($cvs) > self::PAGE_SIZE;
        $cvs = array_slice($cvs, 0, self::PAGE_SIZE);

        if (!$this->isGranted('ROLE_ADMIN')) {
            $cvs = $this->positionEligibilityService->filterAllowedCvs($cvs);
        }

        $likeCounts = $this->cvLikeRepository->countForCvs($cvs);
        $candidates = [];

        foreach ($cvs as $cv) {
            $candidate = $cv->getCandidate();

            if ($candidate !== null && $candidate->getId() !== null) {
                $candidates[$candidate->getId()] = $candidate;
            }
        }

        $candidateNames = $this->userDisplayNameProvider->getForUsers(array_values($candidates));
        $items = [];

        foreach ($cvs as $cv) {
            $candidate = $cv->getCandidate();
            $position = $cv->getPosition();
            $cvId = $cv->getId();
            $candidateId = $candidate?->getId();

            if ($cvId === null || $candidateId === null || $position === null) {
                continue;
            }

            $items[] = [
                'id' => $cvId,
                'candidateId' => $candidateId,
                'candidateName' => $candidateNames[$candidateId] ?? null,
                'positionId' => $position->getId(),
                'positionTitle' => $position->getTitle(),
                'status' => $cv->getStatus()?->value,
                'version' => $cv->getVersion(),
                'likes' => $likeCounts[$cvId] ?? 0,
                'createdAt' => $cv->getCreatedAt()?->format(DATE_ATOM),
                'publishedAt' => $cv->getPublishedAt()?->format(DATE_ATOM),
            ];
        }

        return new JsonResponse([
            'items' => $items,
            'page' => $page,
            'hasMore' => $hasMore,
        ]);
    }
}
