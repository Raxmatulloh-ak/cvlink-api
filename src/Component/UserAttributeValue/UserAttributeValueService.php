<?php

declare(strict_types=1);

namespace App\Component\UserAttributeValue;

use App\Component\UserAttributeValue\Dto\UserAttributeValueWriteDto;
use App\Entity\AttributeOption;
use App\Entity\UserAttributeValue;
use App\Enum\AttributeValueType;
use App\Repository\AttributeOptionRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserAttributeValueService
{
    public function __construct(
        private readonly AttributeOptionRepository $attributeOptionRepository,
    ) {
    }

    public function fill(UserAttributeValue $value, UserAttributeValueWriteDto $data): void
    {
        $this->clear($value);

        match ($value->getAttribute()?->getValueType()) {
            AttributeValueType::STRING, AttributeValueType::TEXT => $value->setTextValue($data->getTextValue()),
            AttributeValueType::IMAGE => $value->setImageReference($data->getImageReference()),
            AttributeValueType::NUMERIC => $value->setNumericValue($data->getNumericValue()),
            AttributeValueType::DATE => $value->setDateValue($data->getDateValue()),
            AttributeValueType::PERIOD => $this->setPeriod($value, $data),
            AttributeValueType::BOOLEAN => $value->setBooleanValue($data->getBooleanValue()),
            AttributeValueType::DROPDOWN => $value->setOption($this->getOption($value, $data->getOptionId())),
            default => throw new BadRequestHttpException('Attribute type is not supported'),
        };
    }

    public function isFilled(UserAttributeValue $value): bool
    {
        return match ($value->getAttribute()?->getValueType()) {
            AttributeValueType::STRING, AttributeValueType::TEXT => trim((string) $value->getTextValue()) !== '',
            AttributeValueType::IMAGE => $value->getImageReference() !== null,
            AttributeValueType::NUMERIC => $value->getNumericValue() !== null,
            AttributeValueType::DATE => $value->getDateValue() !== null,
            AttributeValueType::PERIOD => $value->getPeriodStart() !== null && $value->getPeriodEnd() !== null,
            AttributeValueType::BOOLEAN => $value->isBooleanValue() !== null,
            AttributeValueType::DROPDOWN => $value->getOption() !== null,
            default => false,
        };
    }

    private function setPeriod(UserAttributeValue $value, UserAttributeValueWriteDto $data): UserAttributeValue
    {
        if ($data->getPeriodStart() !== null && $data->getPeriodEnd() !== null && $data->getPeriodStart() > $data->getPeriodEnd()) {
            throw new BadRequestHttpException('Period start cannot be after period end');
        }

        return $value
            ->setPeriodStart($data->getPeriodStart())
            ->setPeriodEnd($data->getPeriodEnd());
    }

    private function getOption(UserAttributeValue $value, ?int $optionId): ?AttributeOption
    {
        if ($optionId === null) {
            return null;
        }

        $option = $this->attributeOptionRepository->find($optionId);

        if ($option === null || $option->getAttribute() !== $value->getAttribute()) {
            throw new BadRequestHttpException('Attribute option not found');
        }

        return $option;
    }

    private function clear(UserAttributeValue $value): void
    {
        $value
            ->setTextValue(null)
            ->setImageReference(null)
            ->setNumericValue(null)
            ->setDateValue(null)
            ->setPeriodStart(null)
            ->setPeriodEnd(null)
            ->setBooleanValue(null)
            ->setOption(null);
    }
}
