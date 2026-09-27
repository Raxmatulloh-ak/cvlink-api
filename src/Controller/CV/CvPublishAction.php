<?php

declare(strict_types=1);

namespace App\Controller\CV;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\CV\CvAccessService;
use App\Component\CV\CvManager;
use App\Component\CV\CvRenderer;
use App\Component\CV\Dto\CvPublishDto;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Enum\CvStatus;
use App\Repository\CVRepository;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CvPublishAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly CVRepository $cvRepository,
        private readonly CvAccessService $cvAccessService,
        private readonly CvRenderer $cvRenderer,
        private readonly CvManager $cvManager,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(CvPublishDto $data, Request $request): JsonResponse
    {
        $this->validate($data);
        $cv = $this->cvRepository->find($request->attributes->getInt('id'));

        if ($cv === null) {
            throw new NotFoundHttpException('CV not found');
        }

        $user = $this->getUser();

        if (!$user instanceof User || !$this->cvAccessService->canManage($cv, $user)) {
            throw new AccessDeniedHttpException();
        }

        if ($cv->getVersion() !== $data->getVersion()) {
            throw new ConflictHttpException('CV was modified');
        }

        $rendered = $this->cvRenderer->render($cv);

        if (!$rendered['complete']) {
            throw new BadRequestHttpException('Fill all CV attributes before publishing');
        }

        $cv
            ->setStatus(CvStatus::PUBLISHED)
            ->setPublishedAt(new \DateTimeImmutable());

        try {
            $this->cvManager->save($cv, true);
        } catch (OptimisticLockException $exception) {
            throw new ConflictHttpException('CV was modified', $exception);
        }

        $rendered['status'] = $cv->getStatus()?->value;
        $rendered['version'] = $cv->getVersion();
        $rendered['publishedAt'] = $cv->getPublishedAt()?->format(DATE_ATOM);

        return new JsonResponse($rendered);
    }
}
