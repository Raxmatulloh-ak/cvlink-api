<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\AccessRuleOperator;
use App\Repository\PositionAccessRuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PositionAccessRuleRepository::class)]
#[ApiResource]
class PositionAccessRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accessRules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AttributeDefinition $attribute = null;

    #[ORM\Column(enumType: AccessRuleOperator::class)]
    private ?AccessRuleOperator $operation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textOperand = null;

    #[ORM\Column(nullable: true)]
    private ?int $numericOperand = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateOperand = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $periodStartOperand = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $periodEndOperand = null;

    #[ORM\Column(nullable: true)]
    private ?bool $boolenOperand = null;

    #[ORM\ManyToOne]
    private ?AttributeOption $option = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getAttribute(): ?AttributeDefinition
    {
        return $this->attribute;
    }

    public function setAttribute(?AttributeDefinition $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getOperation(): ?AccessRuleOperator
    {
        return $this->operation;
    }

    public function setOperation(AccessRuleOperator $operation): static
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

    public function getNumericOperand(): ?int
    {
        return $this->numericOperand;
    }

    public function setNumericOperand(?int $numericOperand): static
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

    public function isBoolenOperand(): ?bool
    {
        return $this->boolenOperand;
    }

    public function setBoolenOperand(?bool $boolenOperand): static
    {
        $this->boolenOperand = $boolenOperand;

        return $this;
    }

    public function getOption(): ?AttributeOption
    {
        return $this->option;
    }

    public function setOption(?AttributeOption $option): static
    {
        $this->option = $option;

        return $this;
    }
}
