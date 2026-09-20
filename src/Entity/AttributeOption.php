<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AttributeOptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AttributeOptionRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['attribute_option:read']],
    denormalizationContext: ['groups' => ['attribute_option:write']],
)]
class AttributeOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['attribute_option:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?AttributeDefinition $attribute = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['attribute_option:read', 'attribute_option:write'])]
    private ?string $label = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['attribute_option:read', 'attribute_option:write'])]
    private ?int $displayOrder = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(?int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }
}
