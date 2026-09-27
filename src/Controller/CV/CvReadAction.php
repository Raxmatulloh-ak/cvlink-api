<?php

declare(strict_types=1);

namespace App\Controller\CV;

use App\Component\CV\CvAccessService;
use App\Component\CV\CvRenderer;
use App\Entity\User;
use App\Repository\CVRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CvReadAction extends AbstractController
{
    public function __construct(
        private readonly CVRepository $cvRepository,
        private readonly CvAccessService $cvAccessService,
        private readonly CvRenderer $cvRenderer,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $cv = $this->cvRepository->find($id);
        $user = $this->getUser();

        if ($cv === null || !$user instanceof User || !$this->cvAccessService->canRead($cv, $user)) {
            throw new NotFoundHttpException('CV not found');
        }

        return new JsonResponse($this->cvRenderer->render($cv));
    }
}
