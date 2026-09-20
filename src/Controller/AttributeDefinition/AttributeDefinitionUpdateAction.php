<?php

declare(strict_types=1);

namespace App\Controller\AttributeDefinition;

use App\Component\AttributeDefinition\AttributeDefinitionManager;
use App\Component\AttributeDefinition\Dto\AttributeDefinitionUpdateDto;
use App\Entity\AttributeDefinition;
use App\Repository\AttributeDefinitionRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AttributeDefinitionUpdateAction
{
    public function __invoke(
        AttributeDefinitionUpdateDto $data,
        Request $request,
        AttributeDefinitionRepository $attributeDefinitionRepository,
        AttributeDefinitionManager $attributeDefinitionManager,
    ): AttributeDefinition {
        $attributeDefinition = $attributeDefinitionRepository->find(
            $request->attributes->getInt('id')
        );

        if ($attributeDefinition === null) {
            throw new NotFoundHttpException('Attribute not found');
        }

        if ($attributeDefinition->getVersion() !== $data->getVersion()) {
            throw new ConflictHttpException(
                'Attribute was modified by another user'
            );
        }

        $name = trim($data->getName());
        $existingAttribute = $attributeDefinitionRepository->findOneByName($name);

        if ($existingAttribute !== null && $existingAttribute->getId() !== $attributeDefinition->getId()) {
            throw new ConflictHttpException('Attribute with this name already exists');
        }

        $attributeDefinition
            ->setCategory($data->getCategory())
            ->setName($name)
            ->setDescription($data->getDescription());

        try {
            $attributeDefinitionManager->save($attributeDefinition, true,);
        } catch (OptimisticLockException $exception) {
            throw new ConflictHttpException('Attribute was modified by another user', $exception);
        } catch (UniqueConstraintViolationException $exception) {
            throw new ConflictHttpException('Attribute with this name already exists', $exception);
        }

        return $attributeDefinition;
    }
}
