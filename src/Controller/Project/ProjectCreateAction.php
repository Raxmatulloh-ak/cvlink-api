<?php

declare(strict_types=1);

namespace App\Controller\Project;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\Project\Dto\ProjectWriteDto;
use App\Component\Project\ProjectFactory;
use App\Component\Project\ProjectManager;
use App\Component\Project\ProjectTagService;
use App\Controller\Base\AbstractController;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly ProjectFactory $projectFactory,
        private readonly ProjectManager $projectManager,
        private readonly ProjectTagService $projectTagService,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(ProjectWriteDto $data, Request $request): Project
    {
        $this->validate($data);
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $candidate = $user;
        $candidateId = $request->attributes->getInt('id');

        if ($candidateId > 0) {
            $candidate = $this->userRepository->find($candidateId);

            if ($candidate === null) {
                throw new NotFoundHttpException('User not found');
            }
        }

        if ($data->getEndDate() !== null && $data->getStartDate() > $data->getEndDate()) {
            throw new BadRequestHttpException('Project start date cannot be after end date');
        }

        $project = $this->projectFactory->create(
            $candidate,
            $data->getName(),
            $data->getStartDate(),
            $data->getEndDate(),
            $data->getDescription() ?? '',
        );

        $this->projectManager->save($project);
        $this->projectTagService->replace($project, $data->getTags());
        $this->projectManager->flush();

        return $project;
    }
}
