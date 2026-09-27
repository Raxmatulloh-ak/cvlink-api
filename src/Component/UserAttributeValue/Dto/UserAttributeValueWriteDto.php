<?php

declare(strict_types=1);

namespace App\Component\UserAttributeValue\Dto;

use DateTimeImmutable;

class UserAttributeValueWriteDto
{
    private ?int $attributeId = null;
    private ?string $textValue = null;
    private ?string $imageReference = null;
    private ?float $numericValue = null;
    private ?DateTimeImmutable $dateValue = null;
    private ?DateTimeImmutable $periodStart = null;
    private ?DateTimeImmutable $periodEnd = null;
    private ?bool $booleanValue = null;
    private ?int $optionId = null;

    public function getAttributeId(): ?int
    {
        return $this->attributeId;
    }

    public function setAttributeId(?int $attributeId): static
    {
        $this->attributeId = $attributeId;

        return $this;
    }

    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    public function setTextValue(?string $textValue): static
    {
        $this->textValue = $textValue;

        return $this;
    }

    public function getImageReference(): ?string
    {
        return $this->imageReference;
    }

    public function setImageReference(?string $imageReference): static
    {
        $this->imageReference = $imageReference;

        return $this;
    }

    public function getNumericValue(): ?float
    {
        return $this->numericValue;
    }

    public function setNumericValue(?float $numericValue): static
    {
        $this->numericValue = $numericValue;

        return $this;
    }

    public function getDateValue(): ?DateTimeImmutable
    {
        return $this->dateValue;
    }

    public function setDateValue(?DateTimeImmutable $dateValue): static
    {
        $this->dateValue = $dateValue;

        return $this;
    }

    public function getPeriodStart(): ?DateTimeImmutable
    {
        return $this->periodStart;
    }

    public function setPeriodStart(?DateTimeImmutable $periodStart): static
    {
        $this->periodStart = $periodStart;

        return $this;
    }

    public function getPeriodEnd(): ?DateTimeImmutable
    {
        return $this->periodEnd;
    }

    public function setPeriodEnd(?DateTimeImmutable $periodEnd): static
    {
        $this->periodEnd = $periodEnd;

        return $this;
    }

    public function getBooleanValue(): ?bool
    {
        return $this->booleanValue;
    }

    public function setBooleanValue(?bool $booleanValue): static
    {
        $this->booleanValue = $booleanValue;

        return $this;
    }

    public function getOptionId(): ?int
    {
        return $this->optionId;
    }

    public function setOptionId(?int $optionId): static
    {
        $this->optionId = $optionId;

        return $this;
    }
}
