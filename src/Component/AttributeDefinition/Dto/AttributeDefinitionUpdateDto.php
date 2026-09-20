<?php

declare(strict_types=1);

namespace App\Component\AttributeDefinition\Dto;

use App\Entity\AttributeCategory;
use App\Enum\AttributeValueType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class AttributeDefinitionUpdateDto
{
    #[Assert\NotNull]
    #[Groups(['attribute:update'])]
    private ?AttributeCategory $category = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['attribute:update'])]
    private ?string $name = null;

    #[Groups(['attribute:update'])]
    private ?string $description = null;

    #[Assert\NotNull]
    #[Groups(['attribute:update'])]
    private ?AttributeValueType $valueType = null;

    #[Assert\NotNull]
    #[Groups(['attribute:update'])]
    private ?int $version = null;

    public function getCategory(): ?AttributeCategory
    {
        return $this->category;
    }

    public function setCategory(?AttributeCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getValueType(): ?AttributeValueType
    {
        return $this->valueType;
    }

    public function setValueType(?AttributeValueType $valueType): static
    {
        $this->valueType = $valueType;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): static
    {
        $this->version = $version;

        return $this;
    }
}
