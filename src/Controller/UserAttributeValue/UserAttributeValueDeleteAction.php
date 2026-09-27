<?php

declare(strict_types=1);

namespace App\Controller\UserAttributeValue;

use App\Component\UserAttributeValue\UserAttributeValueManager;
use App\Repository\UserAttributeValueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAttributeValueDeleteAction extends AbstractController
{
    public function __construct(
        private readonly UserAttributeValueRepository $userAttributeValueRepository,
        private readonly UserAttributeValueManager $userAttributeValueManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $value = $this->userAttributeValueRepository->find($request->attributes->getInt('id'));

        if ($value === null) {
            throw new NotFoundHttpException('Attribute value not found');
        }

        if ($value->getAttribute()?->getBuiltinKey() !== null) {
            throw new BadRequestHttpException('Built-in attribute cannot be removed');
        }

        if ($value->getVersion() !== $request->query->getInt('version')) {
            throw new ConflictHttpException('Attribute value was modified');
        }

        $this->userAttributeValueManager->remove($value, true);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
