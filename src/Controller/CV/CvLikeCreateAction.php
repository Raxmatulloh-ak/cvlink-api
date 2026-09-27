<?php

declare(strict_types=1);

namespace App\Controller\CV;

use App\Component\CV\CvAccessService;
use App\Component\CvLike\CvLikeFactory;
use App\Component\CvLike\CvLikeManager;
use App\Entity\User;
use App\Enum\CvStatus;
use App\Repository\CVRepository;
use App\Repository\CvLikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CvLikeCreateAction extends AbstractController
{
    public function __construct(
        private readonly CVRepository $cvRepository,
        private readonly CvLikeRepository $cvLikeRepository,
        private readonly CvLikeFactory $cvLikeFactory,
        private readonly CvLikeManager $cvLikeManager,
        private readonly CvAccessService $cvAccessService,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $user = $this->getUser();
        $cv = $this->cvRepository->find($id);

        if ($cv === null) {
            throw new NotFoundHttpException('CV not found');
        }

        if (!$user instanceof User || (!$this->isGranted('ROLE_RECRUITER') && !$this->isGranted('ROLE_ADMIN'))) {
            throw new AccessDeniedHttpException();
        }

        if ($cv->getStatus() !== CvStatus::PUBLISHED || !$this->cvAccessService->canRead($cv, $user)) {
            throw new NotFoundHttpException('CV not found');
        }

        $like = $this->cvLikeRepository->findOneBy(['cv' => $cv, 'recruiter' => $user,]);
        $status = Response::HTTP_OK;

        if ($like === null) {
            $like = $this->cvLikeFactory->create($cv, $user);
            $this->cvLikeManager->save($like, true);
            $status = Response::HTTP_CREATED;
        }

        return new JsonResponse([
            'liked' => true,
            'likes' => $this->cvLikeRepository->count(['cv' => $cv]),
        ], $status);
    }
}
