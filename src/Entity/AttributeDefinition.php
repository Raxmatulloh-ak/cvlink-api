<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Enum\AttributeValueType;
use App\Repository\AttributeDefinitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttributeDefinitionRepository::class)]
#[ApiResource]
class AttributeDefinition implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait,
        UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attributeDefinitions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AttributeCategory $category = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: AttributeValueType::class)]
    private ?AttributeValueType $valueType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $builtinKey = null;

    #[ORM\Column]
    private ?int $version = 1;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, UserAttributeValue>
     */
    #[ORM\OneToMany(targetEntity: UserAttributeValue::class, mappedBy: 'attribute')]
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

    public function getBuiltinKey(): ?string
    {
        return $this->builtinKey;
    }

    public function setBuiltinKey(string $builtinKey): static
    {
        $this->builtinKey = $builtinKey;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
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
