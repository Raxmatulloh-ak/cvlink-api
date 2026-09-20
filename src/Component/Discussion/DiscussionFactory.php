<?php

declare(strict_types=1);

namespace App\Component\Discussion;

use App\Entity\Discussion;
use App\Entity\Position;
use App\Entity\User;

class DiscussionFactory
{
    public function create(
        Position $position,
        User $author,
        string $message,
    ): Discussion {
        return new Discussion()
            ->setPosition($position)
            ->setAuthor($author)
            ->setMessage($message);
    }
}
