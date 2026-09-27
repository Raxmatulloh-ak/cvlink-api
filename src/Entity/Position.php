<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Component\Discussion\Dto\DiscussionWriteDto;
use App\Component\Position\Dto\PositionUpdateDto;
use App\Component\Position\Dto\PositionWriteDto;
use App\Controller\Discovery\DashboardAction;
use App\Controller\Discovery\SearchAction;
use App\Controller\Discussion\DiscussionCreateAction;
use App\Controller\Discussion\DiscussionListAction;
use App\Controller\Position\PositionCreateAction;
use App\Controller\Position\PositionDeleteAction;
use App\Controller\Position\PositionDuplicateAction;
use App\Controller\Position\PositionListAction;
use App\Controller\Position\PositionReadAction;
use App\Controller\Position\PositionUpdateAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Enum\PositionAccessType;
use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            controller: PositionListAction::class,
            output: false,
            read: false,
        ),
        new Get(
            controller: PositionReadAction::class,
            output: false,
            read: false,
        ),
        new Get(
            uriTemplate: '/positions/{id}/discussions',
            controller: DiscussionListAction::class,
            output: false,
            read: false,
            name: 'position_discussion_list',
        ),
        new Post(
            uriTemplate: '/positions/{id}/discussions',
            controller: DiscussionCreateAction::class,
            input: DiscussionWriteDto::class,
            output: false,
            read: false,
            write: false,
            name: 'position_discussion_create',
        ),
        new Post(
            controller: PositionCreateAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            input: PositionWriteDto::class,
            write: false,
        ),
        new Patch(
            controller: PositionUpdateAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            input: PositionUpdateDto::class,
            write: false,
        ),
        new Delete(
            controller: PositionDeleteAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            write: false,
        ),
        new Post(
            uriTemplate: '/positions/{id}/duplicate',
            controller: PositionDuplicateAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            input: false,
            write: false,
            name: 'position_duplicate',
        ),
        new Get(
            uriTemplate: '/dashboard',
            controller: DashboardAction::class,
            output: false,
            read: false,
            name: 'dashboard',
        ),
        new Get(
            uriTemplate: '/search',
            controller: SearchAction::class,
            output: false,
            read: false,
            name: 'search',
        ),
    ],
    normalizationContext: ['groups' => ['position:read']],
    denormalizationContext: ['groups' => ['position:write']],
)]
class Position implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait, UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['position:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['position:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['position:read'])]
    private ?string $description = null;

    #[ORM\Column(enumType: PositionAccessType::class)]
    #[Assert\NotNull]
    #[Groups(['position:read'])]
    private ?PositionAccessType $accessType = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Groups(['position:read'])]
    private int $maxProjects = 0;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['position:read'])]
    private int $version = 1;

    #[ORM\Column]
    #[Groups(['position:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, PositionAttribute>
     */
    #[ORM\OneToMany(targetEntity: PositionAttribute::class, mappedBy: 'position')]
    #[ORM\OrderBy(['displayOrder' => 'ASC'])]
    #[Groups(['position:read'])]
    private Collection $positionAttributes;

    /**
     * @var Collection<int, PositionAccessRule>
     */
    #[ORM\OneToMany(targetEntity: PositionAccessRule::class, mappedBy: 'position')]
    #[Groups(['position:read'])]
    private Collection $accessRules;

    /**
     * @var Collection<int, PositionTag>
     */
    #[ORM\OneToMany(targetEntity: PositionTag::class, mappedBy: 'position')]
    #[Groups(['position:read'])]
    private Collection $positionTags;

    /**
     * @var Collection<int, CV>
     */
    #[ORM\OneToMany(targetEntity: CV::class, mappedBy: 'position', orphanRemoval: true)]
    private Collection $cvs;

    /**
     * @var Collection<int, Discussion>
     */
    #[ORM\OneToMany(targetEntity: Discussion::class, mappedBy: 'position', orphanRemoval: true)]
    private Collection $discussions;

    public function __construct()
    {
        $this->positionAttributes = new ArrayCollection();
        $this->accessRules = new ArrayCollection();
        $this->positionTags = new ArrayCollection();
        $this->cvs = new ArrayCollection();
        $this->discussions = new ArrayCollection();
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

    public function getMaxProjects(): int
    {
        return $this->maxProjects;
    }

    public function setMaxProjects(int $maxProjects): static
    {
        $this->maxProjects = $maxProjects;

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
        $this->positionAttributes->removeElement($positionAttribute);

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
        $this->accessRules->removeElement(
            $accessRule
        );

        return $this;
    }

    /**
     * @return Collection<int, PositionTag>
     */
    public function getPositionTags(): Collection
    {
        return $this->positionTags;
    }

    public function addPositionTag(PositionTag $positionTag): static
    {
        if (!$this->positionTags->contains($positionTag)) {
            $this->positionTags->add($positionTag);
            $positionTag->setPosition($this);
        }

        return $this;
    }

    public function removePositionTag(PositionTag $positionTag): static
    {
        $this->positionTags->removeElement(
            $positionTag
        );

        return $this;
    }

    /**
     * @return Collection<int, CV>
     */
    public function getCvs(): Collection
    {
        return $this->cvs;
    }

    public function addCv(CV $cv): static
    {
        if (!$this->cvs->contains($cv)) {
            $this->cvs->add($cv);
            $cv->setPosition($this);
        }

        return $this;
    }

    public function removeCv(CV $cv): static
    {
        if ($this->cvs->removeElement($cv)) {
            // set the owning side to null (unless already changed)
            if ($cv->getPosition() === $this) {
                $cv->setPosition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): static
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setPosition($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): static
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getPosition() === $this) {
                $discussion->setPosition(null);
            }
        }

        return $this;
    }
}
