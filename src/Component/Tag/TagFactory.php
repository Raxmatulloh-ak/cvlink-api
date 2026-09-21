<?php

declare(strict_types=1);

namespace App\Component\Tag;

use App\Entity\Tag;

class TagFactory
{
    public function create(string $name): Tag
    {
        return new Tag()
            ->setName(strtolower(trim($name)));
    }
}
