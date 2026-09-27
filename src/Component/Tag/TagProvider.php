<?php

declare(strict_types=1);

namespace App\Component\Tag;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TagProvider
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly TagFactory $tagFactory,
        private readonly TagManager $tagManager,
    ) {
    }

    public function normalize(string $name): string
    {
        $name = strtolower(trim($name));

        if ($name === '') {
            throw new BadRequestHttpException('Tag cannot be empty');
        }

        return $name;
    }

    /**
     * @param string[] $names
     * @return array<string, Tag>
     */
    public function getOrCreateMany(array $names): array
    {
        $normalized = [];

        foreach ($names as $name) {
            $name = $this->normalize($name);
            $normalized[$name] = $name;
        }

        if ($normalized === []) {
            return [];
        }

        $tags = [];

        foreach ($this->tagRepository->findByNames(array_values($normalized)) as $tag) {
            if ($tag->getName() !== null) {
                $tags[$tag->getName()] = $tag;
            }
        }

        foreach ($normalized as $name) {
            if (isset($tags[$name])) {
                continue;
            }

            $tag = $this->tagFactory->create($name);
            $this->tagManager->save($tag);
            $tags[$name] = $tag;
        }

        return $tags;
    }
}
