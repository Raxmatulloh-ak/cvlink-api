<?php

declare(strict_types=1);

namespace App\Controller\Project;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\Project\Dto\ProjectUpdateDto;
use App\Component\Project\ProjectManager;
use App\Component\Project\ProjectTagService;
use App\Controller\Base\AbstractController;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectUpdateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly ProjectRepository $projectRepository,
        private readonly ProjectManager $projectManager,
        private readonly ProjectTagService $projectTagService,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(ProjectUpdateDto $data, Request $request): Project
    {
        $this->validate($data);
        $project = $this->projectRepository->find($request->attributes->getInt('id'));

        if ($project === null) {
            throw new NotFoundHttpException('Project not found');
        }

        if ($project->getVersion() !== $data->getVersion()) {
            throw new ConflictHttpException('Project was modified');
        }

        if ($data->getEndDate() !== null && $data->getStartDate() > $data->getEndDate()) {
            throw new BadRequestHttpException('Project start date cannot be after end date');
        }

        $user = $this->getUser();

        $project
            ->setName($data->getName())
            ->setStartDate($data->getStartDate())
            ->setEndDate($data->getEndDate())
            ->setDescription($data->getDescription());

        $project->setUpdatedBy($user);
        $this->projectTagService->replace($project, $data->getTags());

        try {
            $this->projectManager->save($project, true);
        } catch (OptimisticLockException $exception) {
            throw new ConflictHttpException('Project was modified', $exception);
        }

        return $project;
    }
}
