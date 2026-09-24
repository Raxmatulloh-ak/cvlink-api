<?php

declare(strict_types=1);

namespace App\Component\Position\Dto;

use App\Enum\AccessRuleOperator;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PositionAccessRuleDto
{
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['position:write'])]
    private ?int $attributeId = null;

    #[Assert\NotNull]
    #[Groups(['position:write'])]
    private ?AccessRuleOperator $operation = null;

    #[Groups(['position:write'])]
    private ?string $textOperand = null;

    #[Groups(['position:write'])]
    private ?float $numericOperand = null;

    #[Groups(['position:write'])]
    private ?\DateTimeImmutable $dateOperand = null;

    #[Groups(['position:write'])]
    private ?\DateTimeImmutable $periodStartOperand = null;

    #[Groups(['position:write'])]
    private ?\DateTimeImmutable $periodEndOperand = null;

    #[Groups(['position:write'])]
    private ?bool $booleanOperand = null;

    #[Assert\Positive]
    #[Groups(['position:write'])]
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

    public function getOperation(): ?AccessRuleOperator
    {
        return $this->operation;
    }

    public function setOperation(?AccessRuleOperator $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    public function getTextOperand(): ?string
    {
        return $this->textOperand;
    }

    public function setTextOperand(?string $textOperand): static
    {
        $this->textOperand = $textOperand;

        return $this;
    }

    public function getNumericOperand(): ?float
    {
        return $this->numericOperand;
    }

    public function setNumericOperand(?float $numericOperand): static
    {
        $this->numericOperand = $numericOperand;

        return $this;
    }

    public function getDateOperand(): ?\DateTimeImmutable
    {
        return $this->dateOperand;
    }

    public function setDateOperand(?\DateTimeImmutable $dateOperand): static
    {
        $this->dateOperand = $dateOperand;

        return $this;
    }

    public function getPeriodStartOperand(): ?\DateTimeImmutable
    {
        return $this->periodStartOperand;
    }

    public function setPeriodStartOperand(?\DateTimeImmutable $periodStartOperand): static
    {
        $this->periodStartOperand = $periodStartOperand;

        return $this;
    }

    public function getPeriodEndOperand(): ?\DateTimeImmutable
    {
        return $this->periodEndOperand;
    }

    public function setPeriodEndOperand(?\DateTimeImmutable $periodEndOperand): static
    {
        $this->periodEndOperand = $periodEndOperand;

        return $this;
    }

    public function getBooleanOperand(): ?bool
    {
        return $this->booleanOperand;
    }

    public function setBooleanOperand(?bool $booleanOperand): static
    {
        $this->booleanOperand = $booleanOperand;

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
