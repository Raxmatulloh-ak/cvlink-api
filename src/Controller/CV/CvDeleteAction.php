<?php

declare(strict_types=1);

namespace App\Controller\CV;

use App\Component\CV\CvAccessService;
use App\Component\CV\CvManager;
use App\Entity\User;
use App\Repository\CVRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CvDeleteAction extends AbstractController
{
    public function __construct(
        private readonly CVRepository $cvRepository,
        private readonly CvAccessService $cvAccessService,
        private readonly CvManager $cvManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $cv = $this->cvRepository->find($request->attributes->getInt('id'));

        if ($cv === null) {
            throw new NotFoundHttpException('CV not found');
        }

        $user = $this->getUser();

        if (!$user instanceof User || !$this->cvAccessService->canManage($cv, $user)) {
            throw new AccessDeniedHttpException();
        }

        if ($cv->getVersion() !== $request->query->getInt('version')) {
            throw new ConflictHttpException('CV was modified');
        }

        $this->cvManager->remove($cv, true);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
