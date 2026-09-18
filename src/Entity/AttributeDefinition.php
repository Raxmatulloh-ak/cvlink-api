<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\AttributeValueType;
use App\Repository\AttributeDefinitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttributeDefinitionRepository::class)]
#[ApiResource]
class AttributeDefinition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attributeDefinitions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AttributeCategory $category = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(enumType: AttributeValueType::class)]
    private ?AttributeValueType $valueType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bultinKey = null;

    #[ORM\Column]
    private ?int $version = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, UserAttributeValue>
     */
    #[ORM\OneToMany(targetEntity: UserAttributeValue::class, mappedBy: 'attribute', orphanRemoval: true)]
    private Collection $userAttributeValues;

    public function __construct()
    {
        $this->userAttributeValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getValueType(): ?AttributeValueType
    {
        return $this->valueType;
    }

    public function setValueType(AttributeValueType $valueType): static
    {
        $this->valueType = $valueType;

        return $this;
    }

    public function getBultinKey(): ?string
    {
        return $this->bultinKey;
    }

    public function setBultinKey(string $bultinKey): static
    {
        $this->bultinKey = $bultinKey;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, UserAttributeValue>
     */
    public function getUserAttributeValues(): Collection
    {
        return $this->userAttributeValues;
    }

    public function addUserAttributeValue(UserAttributeValue $userAttributeValue): static
    {
        if (!$this->userAttributeValues->contains($userAttributeValue)) {
            $this->userAttributeValues->add($userAttributeValue);
            $userAttributeValue->setAttribute($this);
        }

        return $this;
    }

    public function removeUserAttributeValue(UserAttributeValue $userAttributeValue): static
    {
        if ($this->userAttributeValues->removeElement($userAttributeValue)) {
            // set the owning side to null (unless already changed)
            if ($userAttributeValue->getAttribute() === $this) {
                $userAttributeValue->setAttribute(null);
            }
        }

        return $this;
    }
}
