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

    public function getOrCreate(string $name): Tag
    {
        $name = $this->normalize($name);

        $tag = $this->tagRepository
            ->findOneByName($name);

        if ($tag !== null) {
            return $tag;
        }

        $tag = $this->tagFactory->create($name);
        $this->tagManager->save($tag);

        return $tag;
    }
}
