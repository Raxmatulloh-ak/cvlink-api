<?php

declare(strict_types=1);

namespace App\Component\ProjectTag;

use App\Entity\Project;
use App\Entity\ProjectTag;
use App\Entity\Tag;

class ProjectTagFactory
{
    public function create(
        Project $project,
        Tag $tag,
    ): ProjectTag {
        return new ProjectTag()
            ->setProject($project)
            ->setTag($tag);
    }
}
