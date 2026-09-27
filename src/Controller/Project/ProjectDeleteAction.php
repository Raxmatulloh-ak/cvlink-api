<?php

declare(strict_types=1);

namespace App\Controller\Project;

use App\Component\Project\ProjectManager;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectDeleteAction extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly ProjectManager $projectManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $project = $this->projectRepository->find($request->attributes->getInt('id'));

        if ($project === null) {
            throw new NotFoundHttpException('Project not found');
        }

        if ($project->getVersion() !== $request->query->getInt('version')) {
            throw new ConflictHttpException('Project was modified');
        }

        $this->projectManager->remove($project, true);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
