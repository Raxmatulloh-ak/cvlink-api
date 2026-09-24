<?php

declare(strict_types=1);

namespace App\Controller\Position;

use App\Component\Position\PositionManager;
use App\Controller\Base\AbstractController;
use App\Entity\Position;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class PositionDeleteAction extends AbstractController
{
    public function __invoke(
        Position $data,
        Request $request,
        PositionManager $positionManager,
    ): Response {
        if ($request->query->has('version') === false) {
            throw new BadRequestHttpException('Version is required');
        }

        $version = $request->query->getInt('version');

        if ($version < 1) {
            throw new BadRequestHttpException('Version must be a positive integer');
        }

        if ($data->getVersion() !== $version) {
            throw new ConflictHttpException('Position was modified by another user');
        }

        $positionManager->remove($data, true);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
