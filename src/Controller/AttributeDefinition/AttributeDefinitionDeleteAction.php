<?php

declare(strict_types=1);

namespace App\Controller\AttributeDefinition;

use App\Component\AttributeDefinition\AttributeDefinitionManager;
use App\Controller\Base\AbstractController;
use App\Entity\AttributeDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AttributeDefinitionDeleteAction extends AbstractController
{
    public function __invoke(
        AttributeDefinition $data,
        Request $request,
        AttributeDefinitionManager $attributeDefinitionManager,
    ): Response {
        if ($data->getBuiltinKey() !== null) {
            throw new BadRequestHttpException('Built-in attribute cannot be deleted');
        }

        if (!$request->query->has('version')) {
            throw new BadRequestHttpException('Version is required');
        }

        $version = $request->query->getInt('version');

        if ($data->getVersion() !== $version) {
            throw new ConflictHttpException('Attribute was modified by another user');
        }

        $attributeDefinitionManager->remove($data, true,);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
