<?php

declare(strict_types=1);

namespace App\Controller\AttributeDefinition;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\AttributeDefinition\AttributeDefinitionFactory;
use App\Component\AttributeDefinition\AttributeDefinitionManager;
use App\Component\AttributeOption\AttributeOptionFactory;
use App\Component\AttributeOption\AttributeOptionManager;
use App\Controller\Base\AbstractController;
use App\Entity\AttributeDefinition;
use App\Entity\AttributeOption;
use App\Enum\AttributeValueType;
use App\Repository\AttributeDefinitionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AttributeDefinitionCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly AttributeDefinitionFactory $attributeDefinitionFactory,
        private readonly AttributeDefinitionManager $attributeDefinitionManager,
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
        private readonly AttributeOptionFactory $attributeOptionFactory,
        private readonly AttributeOptionManager $attributeOptionManager,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(AttributeDefinition $data): AttributeDefinition
    {
        $this->validate($data);
        $this->validateName((string)$data->getName());
        $this->validateOptions($data);

        $attribute = $this->attributeDefinitionFactory->create(
            $data->getCategory(),
            $data->getName(),
            $data->getDescription(),
            $data->getValueType(),
        );

        $this->attributeDefinitionManager->save($attribute);
        $this->createOptions($attribute, $data->getOptions());

        try {
            $this->attributeDefinitionManager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            throw new ConflictHttpException('Attribute or option already exists', $exception);
        }

        return $attribute;
    }

    private function validateName(string $name): void
    {
        $attribute = $this->attributeDefinitionRepository->findOneByName(trim($name));

        if ($attribute !== null) {
            throw new ConflictHttpException('Attribute with this name already exists');
        }
    }

    private function validateOptions(AttributeDefinition $data): void
    {
        if (
            $data->getValueType() !== AttributeValueType::DROPDOWN
            && !$data->getOptions()->isEmpty()
        ) {
            throw new BadRequestHttpException('Options are allowed only for dropdown attributes');
        }

        if (
            $data->getValueType() === AttributeValueType::DROPDOWN
            && $data->getOptions()->isEmpty()
        ) {
            throw new BadRequestHttpException('Dropdown attribute must have at least one option');
        }
    }

    /**
     * @param Collection<int, AttributeOption> $options
     */
    private function createOptions(AttributeDefinition $attributeDefinition, Collection $options): void
    {
        foreach ($options as $optionData) {
            $option = $this->attributeOptionFactory->create(
                $attributeDefinition,
                $optionData->getLabel(),
                $optionData->getDisplayOrder(),
            );

            $attributeDefinition->addOption($option);
            $this->attributeOptionManager->save($option);
        }
    }
}
