<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AccessRuleOperator;
use App\Repository\PositionAccessRuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PositionAccessRuleRepository::class)]
class PositionAccessRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['position:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accessRules')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Position $position = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['position:read'])]
    private ?AttributeDefinition $attribute = null;

    #[ORM\Column(enumType: AccessRuleOperator::class)]
    #[Groups(['position:read'])]
    private ?AccessRuleOperator $operation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['position:read'])]
    private ?string $textOperand = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['position:read'])]
    private ?float $numericOperand = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['position:read'])]
    private ?\DateTimeImmutable $dateOperand = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['position:read'])]
    private ?\DateTimeImmutable $periodStartOperand = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['position:read'])]
    private ?\DateTimeImmutable $periodEndOperand = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['position:read'])]
    private ?bool $booleanOperand = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['position:read'])]
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

    public function isBooleanOperand(): ?bool
    {
        return $this->booleanOperand;
    }

    public function setBooleanOperand(?bool $booleanOperand): static
    {
        $this->booleanOperand = $booleanOperand;

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
