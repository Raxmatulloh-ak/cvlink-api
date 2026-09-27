<?php

declare(strict_types=1);

namespace App\Controller\Discussion;

use App\Component\Position\PositionEligibilityService;
use App\Component\User\UserDisplayNameProvider;
use App\Entity\User;
use App\Repository\DiscussionRepository;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DiscussionListAction extends AbstractController
{
    public function __construct(
        private readonly PositionRepository $positionRepository,
        private readonly DiscussionRepository $discussionRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
        private readonly UserDisplayNameProvider $userDisplayNameProvider,
    ) {
    }

    public function __invoke(int $id, Request $request): JsonResponse
    {
        $position = $this->positionRepository->find($id);
        $user = $this->getUser();

        if ($position === null) {
            throw new NotFoundHttpException('Position not found');
        }

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        if (
            !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_RECRUITER')
            && !$this->positionEligibilityService->isAllowed($position, $user)
        ) {
            throw new NotFoundHttpException('Position not found');
        }

        $after = max(0, $request->query->getInt('after'));
        $discussions = $this->discussionRepository->findAfter($position, $after);
        $authors = [];

        foreach ($discussions as $discussion) {
            $author = $discussion->getAuthor();

            if ($author !== null && $author->getId() !== null) {
                $authors[$author->getId()] = $author;
            }
        }

        $names = $this->userDisplayNameProvider->getForUsers(array_values($authors));
        $items = [];

        foreach ($discussions as $discussion) {
            $authorId = $discussion->getAuthor()?->getId();

            $items[] = [
                'id' => $discussion->getId(),
                'authorId' => $authorId,
                'authorName' => $authorId === null ? null : ($names[$authorId] ?? null),
                'message' => $discussion->getMessage(),
                'createdAt' => $discussion->getCreatedAt()?->format(DATE_ATOM),
            ];
        }

        $last = end($items);

        return new JsonResponse([
            'items' => $items,
            'nextAfter' => $last === false ? $after : $last['id'],
            'pollAfterSeconds' => 3,
        ]);
    }
}
