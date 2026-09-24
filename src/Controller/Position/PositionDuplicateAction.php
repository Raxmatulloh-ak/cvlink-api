<?php

declare(strict_types=1);

namespace App\Controller\Position;

use App\Component\Position\PositionDuplicator;
use App\Entity\Position;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PositionDuplicateAction extends AbstractController
{
    public function __invoke(Position $data, PositionDuplicator $positionDuplicator): Position
    {
        $user = $this->getUser();

        if ($user instanceof User === false) {
            $user = null;
        }

        return $positionDuplicator->duplicate($data, $user);
    }
}
