<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource]
class Project implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait, UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userProjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $candidate = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version = 1;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, ProjectTag>
     */
    #[ORM\OneToMany(
        targetEntity: ProjectTag::class,
        mappedBy: 'project',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $projectTags;

    public function __construct()
    {
        $this->projectTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidate(): ?User
    {
        return $this->candidate;
    }

    public function setCandidate(?User $candidate): static
    {
        $this->candidate = $candidate;

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

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getVersion(): int
    {
        return $this->version;
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
     * @return Collection<int, ProjectTag>
     */
    public function getProjectTags(): Collection
    {
        return $this->projectTags;
    }

    public function addProjectTag(ProjectTag $projectTag): static
    {
        if (!$this->projectTags->contains($projectTag)) {
            $this->projectTags->add($projectTag);
            $projectTag->setProject($this);
        }

        return $this;
    }

    public function removeProjectTag(ProjectTag $projectTag): static
    {
        if ($this->projectTags->removeElement($projectTag)) {
            // set the owning side to null (unless already changed)
            if ($projectTag->getProject() === $this) {
                $projectTag->setProject(null);
            }
        }

        return $this;
    }
}
