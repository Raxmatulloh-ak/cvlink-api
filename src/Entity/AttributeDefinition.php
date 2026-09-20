<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Component\AttributeDefinition\Dto\AttributeDefinitionUpdateDto;
use App\Controller\AttributeDefinition\AttributeDefinitionCreateAction;
use App\Controller\AttributeDefinition\AttributeDefinitionDeleteAction;
use App\Controller\AttributeDefinition\AttributeDefinitionUpdateAction;
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
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AttributeDefinitionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            controller: AttributeDefinitionCreateAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
        ),
        new Patch(
            controller: AttributeDefinitionUpdateAction::class,
            denormalizationContext: ['groups' => ['attribute:update']],
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            input: AttributeDefinitionUpdateDto::class,
            write: false,
        ),
        new Delete(
            controller: AttributeDefinitionDeleteAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            write: false,
        ),
    ],
    normalizationContext: ['groups' => ['attribute:read']],
    denormalizationContext: ['groups' => ['attribute:write']],
)]
class AttributeDefinition implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait, UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['attribute:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attributeDefinitions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['attribute:read', 'attribute:write', 'attribute:update'])]
    private ?AttributeCategory $category = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['attribute:read', 'attribute:write', 'attribute:update'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['attribute:read', 'attribute:write', 'attribute:update'])]
    private ?string $description = null;

    #[ORM\Column(enumType: AttributeValueType::class)]
    #[Assert\NotNull]
    #[Groups(['attribute:write'])]
    private ?AttributeValueType $valueType = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Groups(['attribute:read', 'attribute:write'])]
    private ?string $builtinKey = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['attribute:read'])]
    private int $version = 1;

    #[ORM\Column]
    #[Groups(['attribute:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, UserAttributeValue>
     */
    #[ORM\OneToMany(targetEntity: UserAttributeValue::class, mappedBy: 'attribute')]
    #[Groups(['attribute:read'])]
    private Collection $userAttributeValues;

    /**
     * @var Collection<int, AttributeOption>
     */
    #[ORM\OneToMany(
        targetEntity: AttributeOption::class,
        mappedBy: 'attribute',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    #[Groups(['attribute:read', 'attribute:write'])]
    private Collection $options;

    public function __construct()
    {
        $this->userAttributeValues = new ArrayCollection();
        $this->options = new ArrayCollection();
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

    public function setDescription(?string $description): static
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

    public function setBuiltinKey(?string $builtinKey): static
    {
        $this->builtinKey = $builtinKey;

        return $this;
    }

    public function getVersion(): int
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

    /**
     * @return Collection<int, AttributeOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(AttributeOption $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setAttribute($this);
        }

        return $this;
    }

    public function removeOption(AttributeOption $option): static
    {
        if ($this->options->removeElement($option)) {
            if ($option->getAttribute() === $this) {
                $option->setAttribute(null);
            }
        }

        return $this;
    }
}
