<?php

declare(strict_types=1);

namespace App\Controller\CV;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\CV\CvFactory;
use App\Component\CV\CvManager;
use App\Component\CV\CvRenderer;
use App\Component\CV\Dto\CvCreateDto;
use App\Component\Position\PositionEligibilityService;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\CVRepository;
use App\Repository\PositionRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CvCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly PositionRepository $positionRepository,
        private readonly UserRepository $userRepository,
        private readonly CVRepository $cvRepository,
        private readonly PositionEligibilityService $positionEligibilityService,
        private readonly CvFactory $cvFactory,
        private readonly CvManager $cvManager,
        private readonly CvRenderer $cvRenderer,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(CvCreateDto $data): JsonResponse
    {
        $this->validate($data);
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $candidate = $user;

        if ($data->getCandidateId() !== null) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException();
            }

            $candidate = $this->userRepository->find($data->getCandidateId())
                ?? throw new NotFoundHttpException('Candidate not found');
        }

        if (!in_array('ROLE_CANDIDATE', $candidate->getRoles(), true)) {
            throw new BadRequestHttpException('User is not a candidate');
        }

        $position = $this->positionRepository->find($data->getPositionId())
            ?? throw new NotFoundHttpException('Position not found');

        if (!$this->isGranted('ROLE_ADMIN') && !$this->positionEligibilityService->isAllowed($position, $candidate)) {
            throw new AccessDeniedHttpException('Position is not available for this candidate');
        }

        if ($this->cvRepository->findOneBy(['candidate' => $candidate, 'position' => $position]) !== null) {
            throw new ConflictHttpException('CV for this position already exists');
        }

        $cv = $this->cvFactory->create($candidate, $position);
        $this->cvManager->save($cv, true);

        return new JsonResponse($this->cvRenderer->render($cv), Response::HTTP_CREATED,);
    }
}
