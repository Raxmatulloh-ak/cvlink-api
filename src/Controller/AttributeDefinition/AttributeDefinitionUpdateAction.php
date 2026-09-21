<?php

declare(strict_types=1);

namespace App\Controller\AttributeDefinition;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\AttributeDefinition\AttributeDefinitionManager;
use App\Component\AttributeDefinition\Dto\AttributeDefinitionUpdateDto;
use App\Component\AttributeOption\AttributeOptionSynchronizer;
use App\Controller\Base\AbstractController;
use App\Entity\AttributeDefinition;
use App\Repository\AttributeDefinitionRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AttributeDefinitionUpdateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
        private readonly AttributeDefinitionManager $attributeDefinitionManager,
        private readonly AttributeOptionSynchronizer $attributeOptionSynchronizer,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(AttributeDefinitionUpdateDto $data, Request $request): AttributeDefinition
    {
        $this->validate($data);

        $attributeDefinition = $this->attributeDefinitionRepository->find(
            $request->attributes->getInt('id')
        );

        if ($attributeDefinition === null) {
            throw new NotFoundHttpException('Attribute not found');
        }

        $this->validateVersion($attributeDefinition, $data->getVersion());
        $this->validateName($attributeDefinition, $data->getName());

        $attributeDefinition
            ->setCategory($data->getCategory())
            ->setName(trim($data->getName()))
            ->setDescription($data->getDescription());

        $this->attributeOptionSynchronizer->sync($attributeDefinition, $data->getOptions());

        try {
            $this->attributeDefinitionManager->save($attributeDefinition, true);
        } catch (OptimisticLockException $exception) {
            throw new ConflictHttpException('Attribute was modified by another user', $exception);
        } catch (UniqueConstraintViolationException $exception) {
            throw new ConflictHttpException('Attribute or option already exists', $exception);
        } catch (ForeignKeyConstraintViolationException $exception) {
            throw new ConflictHttpException('An option that is already in use cannot be deleted', $exception,);
        }

        return $attributeDefinition;
    }

    private function validateVersion(AttributeDefinition $attributeDefinition, int $version): void
    {
        if ($attributeDefinition->getVersion() !== $version) {
            throw new ConflictHttpException('Attribute was modified by another user');
        }
    }

    private function validateName(AttributeDefinition $attributeDefinition, string $name): void
    {
        $existingAttribute = $this->attributeDefinitionRepository->findOneByName(trim($name));

        if (
            $existingAttribute !== null
            && $existingAttribute->getId() !== $attributeDefinition->getId()
        ) {
            throw new ConflictHttpException('Attribute with this name already exists');
        }
    }
}
