<?php

declare(strict_types=1);

namespace App\Controller\Discussion;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\Discussion\DiscussionFactory;
use App\Component\Discussion\DiscussionManager;
use App\Component\Discussion\Dto\DiscussionWriteDto;
use App\Component\Position\PositionEligibilityService;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\PositionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DiscussionCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly PositionRepository $positionRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
        private readonly DiscussionFactory $discussionFactory,
        private readonly DiscussionManager $discussionManager,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(int $id, DiscussionWriteDto $data): JsonResponse
    {
        $this->validate($data);

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

        $discussion = $this->discussionFactory->create(
            $position,
            $user,
            trim((string) $data->getMessage()),
        );

        $this->discussionManager->save($discussion, true);

        return new JsonResponse([
            'id' => $discussion->getId(),
            'authorId' => $user->getId(),
            'message' => $discussion->getMessage(),
            'createdAt' => $discussion->getCreatedAt()?->format(DATE_ATOM),
        ], Response::HTTP_CREATED);
    }
}
