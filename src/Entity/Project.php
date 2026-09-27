<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Component\Project\Dto\ProjectUpdateDto;
use App\Component\Project\Dto\ProjectWriteDto;
use App\Controller\Project\ProjectCreateAction;
use App\Controller\Project\ProjectDeleteAction;
use App\Controller\Project\ProjectUpdateAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN') || (is_granted('ROLE_CANDIDATE') && object.getCandidate() === user)",
        ),
        new Post(
            controller: ProjectCreateAction::class,
            security: "is_granted('ROLE_CANDIDATE') || is_granted('ROLE_ADMIN')",
            input: ProjectWriteDto::class,
            write: false,
        ),
        new Post(
            uriTemplate: '/users/{id}/projects',
            controller: ProjectCreateAction::class,
            security: "is_granted('ROLE_ADMIN')",
            input: ProjectWriteDto::class,
            read: false,
            write: false,
            name: 'user_project_create',
        ),
        new Patch(
            controller: ProjectUpdateAction::class,
            security: "is_granted('ROLE_ADMIN') || (is_granted('ROLE_CANDIDATE') && object.getCandidate() === user)",
            input: ProjectUpdateDto::class,
            write: false,
        ),
        new Delete(
            controller: ProjectDeleteAction::class,
            security: "is_granted('ROLE_ADMIN') || (is_granted('ROLE_CANDIDATE') && object.getCandidate() === user)",
            write: false,
        ),
    ],
    normalizationContext: ['groups' => ['project:read']],
)]
class Project implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait, UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userProjects')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $candidate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['project:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['project:read'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['project:read'])]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['project:read'])]
    private ?string $description = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['project:read'])]
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
    #[Groups(['project:read'])]
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
