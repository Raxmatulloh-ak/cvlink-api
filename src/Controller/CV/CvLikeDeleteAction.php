<?php

declare(strict_types=1);

namespace App\Controller\CV;

use App\Component\CV\CvAccessService;
use App\Component\CvLike\CvLikeManager;
use App\Entity\User;
use App\Enum\CvStatus;
use App\Repository\CVRepository;
use App\Repository\CvLikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CvLikeDeleteAction extends AbstractController
{
    public function __construct(
        private readonly CVRepository $cvRepository,
        private readonly CvLikeRepository $cvLikeRepository,
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

        if ($like !== null) {
            $this->cvLikeManager->remove($like, true);
        }

        return new JsonResponse([
            'liked' => false,
            'likes' => $this->cvLikeRepository->count(['cv' => $cv]),
        ]);
    }
}
