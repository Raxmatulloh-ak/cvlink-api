<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PositionAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PositionAttributeRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_position_attribute', columns: ['position_id', 'attribute_id'])]
class PositionAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['position:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'positionAttributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private ?Position $position = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    #[Groups(['position:read'])]
    private ?AttributeDefinition $attribute = null;

    #[ORM\Column]
    #[Groups(['position:read'])]
    private int $displayOrder = 0;

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

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }
}
