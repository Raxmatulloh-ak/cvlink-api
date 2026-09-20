<?php

declare(strict_types=1);

namespace App\Controller\AttributeDefinition;

use App\Component\AttributeDefinition\AttributeDefinitionFactory;
use App\Component\AttributeDefinition\AttributeDefinitionManager;
use App\Entity\AttributeDefinition;
use App\Repository\AttributeDefinitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AttributeDefinitionCreateAction extends AbstractController
{
    public function __construct(
        private readonly AttributeDefinitionFactory $attributeDefinitionFactory,
        private readonly AttributeDefinitionManager $attributeDefinitionManager,
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
    ) {
    }

    public function __invoke(AttributeDefinition $data): AttributeDefinition
    {
        if ($this->attributeDefinitionRepository->findOneByName($data->getName())) {
            throw new BadRequestHttpException('Attribute already exists');
        }

        $attribute = $this->attributeDefinitionFactory->create(
            $data->getCategory(),
            $data->getName(),
            $data->getDescription(),
            $data->getValueType(),
        );

        $this->attributeDefinitionManager->save($attribute, true,);

        return $attribute;
    }
}
