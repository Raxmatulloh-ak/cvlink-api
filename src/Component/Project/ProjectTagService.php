<?php

declare(strict_types=1);

namespace App\Component\Project;

use App\Component\ProjectTag\ProjectTagFactory;
use App\Component\ProjectTag\ProjectTagManager;
use App\Component\Tag\TagProvider;
use App\Entity\Project;

class ProjectTagService
{
    public function __construct(
        private readonly TagProvider $tagProvider,
        private readonly ProjectTagFactory $projectTagFactory,
        private readonly ProjectTagManager $projectTagManager,
    ) {
    }

    /**
     * @param string[] $tagNames
     */
    public function replace(Project $project, array $tagNames): void
    {
        $tags = $this->tagProvider->getOrCreateMany($tagNames);

        foreach ($project->getProjectTags()->toArray() as $projectTag) {
            $project->removeProjectTag($projectTag);
            $this->projectTagManager->remove($projectTag);
        }

        foreach ($tags as $tag) {
            $projectTag = $this->projectTagFactory->create($project, $tag);

            $project->addProjectTag($projectTag);
            $this->projectTagManager->save($projectTag);
        }
    }
}
