<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AttributeCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttributeCategoryRepository::class)]
#[ApiResource]
class AttributeCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, unique: true)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $displayOrder = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, AttributeDefinition>
     */
    #[ORM\OneToMany(targetEntity: AttributeDefinition::class, mappedBy: 'category')]
    private Collection $attributeDefinitions;

    public function __construct()
    {
        $this->attributeDefinitions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, AttributeDefinition>
     */
    public function getAttributeDefinitions(): Collection
    {
        return $this->attributeDefinitions;
    }

    public function addAttributeDefinition(AttributeDefinition $attributeDefinition): static
    {
        if (!$this->attributeDefinitions->contains($attributeDefinition)) {
            $this->attributeDefinitions->add($attributeDefinition);
            $attributeDefinition->setCategory($this);
        }

        return $this;
    }

    public function removeAttributeDefinition(AttributeDefinition $attributeDefinition): static
    {
        if ($this->attributeDefinitions->removeElement($attributeDefinition)) {
            // set the owning side to null (unless already changed)
            if ($attributeDefinition->getCategory() === $this) {
                $attributeDefinition->setCategory(null);
            }
        }

        return $this;
    }
}
