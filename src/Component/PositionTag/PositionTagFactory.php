<?php

declare(strict_types=1);

namespace App\Component\PositionTag;

use App\Entity\Position;
use App\Entity\PositionTag;
use App\Entity\Tag;

class PositionTagFactory
{
    public function create(
        Position $position,
        Tag $tag,
    ): PositionTag {
        return new PositionTag()
            ->setPosition($position)
            ->setTag($tag);
    }
}
