<?php

declare(strict_types=1);

namespace App\Component\PositionTag;

use App\Component\Tag\TagProvider;
use App\Entity\Position;
use App\Entity\PositionTag;

class PositionTagService
{
    public function __construct(
        private readonly TagProvider $tagProvider,
        private readonly PositionTagFactory $positionTagFactory,
        private readonly PositionTagManager $positionTagManager,
    ) {
    }

    /**
     * @param string[] $tags
     */
    public function replace(Position $position, array $tags): void
    {
        $existing = $this->getExisting($position);
        $received = [];

        foreach ($tags as $name) {
            $name = $this->tagProvider->normalize($name);

            if (isset($received[$name])) {
                continue;
            }

            $received[$name] = true;

            if (isset($existing[$name])) {
                unset($existing[$name]);

                continue;
            }

            $tag = $this->tagProvider->getOrCreate($name);

            $positionTag = $this->positionTagFactory->create(
                $position,
                $tag,
            );

            $position->addPositionTag($positionTag);
            $this->positionTagManager->save($positionTag);
        }

        foreach ($existing as $positionTag) {
            $position->removePositionTag($positionTag);
            $this->positionTagManager->remove($positionTag);
        }
    }

    public function copy(Position $source, Position $target): void
    {
        foreach ($source->getPositionTags() as $sourceTag) {
            $positionTag = $this->positionTagFactory->create(
                $target,
                $sourceTag->getTag(),
            );

            $target->addPositionTag($positionTag);
            $this->positionTagManager->save($positionTag);
        }
    }

    /**
     * @return array<string, PositionTag>
     */
    private function getExisting(Position $position): array
    {
        $existing = [];

        foreach ($position->getPositionTags() as $positionTag) {
            $name = $positionTag->getTag()?->getName();

            if ($name !== null) {
                $existing[$name] = $positionTag;
            }
        }

        return $existing;
    }
}
