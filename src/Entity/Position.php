<?php

namespace App\Entity;

use App\Enum\PositionAccessType;
use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
class Position
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: PositionAccessType::class)]
    private ?PositionAccessType $accessType = null;

    #[ORM\Column]
    private ?int $maxProjects = null;

    #[ORM\Column]
    private ?int $version = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, PositionAttribute>
     */
    #[ORM\OneToMany(targetEntity: PositionAttribute::class, mappedBy: 'position', orphanRemoval: true)]
    private Collection $positionAttributes;

    /**
     * @var Collection<int, PositionAccessRule>
     */
    #[ORM\OneToMany(targetEntity: PositionAccessRule::class, mappedBy: 'position', orphanRemoval: true)]
    private Collection $accessRules;

    public function __construct()
    {
        $this->positionAttributes = new ArrayCollection();
        $this->accessRules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getAccessType(): ?PositionAccessType
    {
        return $this->accessType;
    }

    public function setAccessType(PositionAccessType $accessType): static
    {
        $this->accessType = $accessType;

        return $this;
    }

    public function getMaxProjects(): ?int
    {
        return $this->maxProjects;
    }

    public function setMaxProjects(int $maxProjects): static
    {
        $this->maxProjects = $maxProjects;

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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection<int, PositionAttribute>
     */
    public function getPositionAttributes(): Collection
    {
        return $this->positionAttributes;
    }

    public function addPositionAttribute(PositionAttribute $positionAttribute): static
    {
        if (!$this->positionAttributes->contains($positionAttribute)) {
            $this->positionAttributes->add($positionAttribute);
            $positionAttribute->setPosition($this);
        }

        return $this;
    }

    public function removePositionAttribute(PositionAttribute $positionAttribute): static
    {
        if ($this->positionAttributes->removeElement($positionAttribute)) {
            // set the owning side to null (unless already changed)
            if ($positionAttribute->getPosition() === $this) {
                $positionAttribute->setPosition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PositionAccessRule>
     */
    public function getAccessRules(): Collection
    {
        return $this->accessRules;
    }

    public function addAccessRule(PositionAccessRule $accessRule): static
    {
        if (!$this->accessRules->contains($accessRule)) {
            $this->accessRules->add($accessRule);
            $accessRule->setPosition($this);
        }

        return $this;
    }

    public function removeAccessRule(PositionAccessRule $accessRule): static
    {
        if ($this->accessRules->removeElement($accessRule)) {
            // set the owning side to null (unless already changed)
            if ($accessRule->getPosition() === $this) {
                $accessRule->setPosition(null);
            }
        }

        return $this;
    }
}
