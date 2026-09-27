<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Component\CV\Dto\CvCreateDto;
use App\Component\CV\Dto\CvPublishDto;
use App\Controller\CV\CvCreateAction;
use App\Controller\CV\CvLikeCreateAction;
use App\Controller\CV\CvLikeDeleteAction;
use App\Controller\CV\CvListAction;
use App\Controller\CV\CvDeleteAction;
use App\Controller\CV\CvPublishAction;
use App\Controller\CV\CvReadAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\UpdatedAtAccessorsTrait;
use App\Enum\CvStatus;
use App\Repository\CVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CVRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/cvs',
            controller: CvListAction::class,
            output: false,
            read: false,
        ),
        new Get(
            uriTemplate: '/cvs/{id}',
            controller: CvReadAction::class,
            output: false,
            read: false,
        ),
        new Post(
            uriTemplate: '/cvs',
            controller: CvCreateAction::class,
            security: "is_granted('ROLE_CANDIDATE') || is_granted('ROLE_ADMIN')",
            input: CvCreateDto::class,
            output: false,
            write: false,
        ),
        new Post(
            uriTemplate: '/cvs/{id}/like',
            controller: CvLikeCreateAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            input: false,
            output: false,
            read: false,
            write: false,
            name: 'cv_like_create',
        ),
        new Delete(
            uriTemplate: '/cvs/{id}/like',
            controller: CvLikeDeleteAction::class,
            security: "is_granted('ROLE_RECRUITER') || is_granted('ROLE_ADMIN')",
            output: false,
            read: false,
            write: false,
            name: 'cv_like_delete',
        ),
        new Post(
            uriTemplate: '/cvs/{id}/publish',
            controller: CvPublishAction::class,
            security: "is_granted('ROLE_CANDIDATE') || is_granted('ROLE_ADMIN')",
            input: CvPublishDto::class,
            output: false,
            read: false,
            write: false,
            name: 'cv_publish',
        ),
        new Delete(
            uriTemplate: '/cvs/{id}',
            controller: CvDeleteAction::class,
            security: "is_granted('ROLE_CANDIDATE') || is_granted('ROLE_ADMIN')",
            output: false,
            read: false,
            write: false,
        ),
    ],
)]
#[ORM\UniqueConstraint(name: 'uniq_candidate_cv', columns: ['candidate_id', 'position_id'])]
class CV implements CreatedAtSettableInterface, UpdatedAtSettableInterface
{
    use CreatedAtAccessorsTrait, UpdatedAtAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cvs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'cvs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Position $position = null;

    #[ORM\Column(enumType: CvStatus::class)]
    private ?CvStatus $status = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version = 1;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    /**
     * @var Collection<int, CvLike>
     */
    #[ORM\OneToMany(targetEntity: CvLike::class, mappedBy: 'cv', orphanRemoval: true)]
    private Collection $likes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
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

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getStatus(): ?CvStatus
    {
        return $this->status;
    }

    public function setStatus(CvStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, CvLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(CvLike $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setCv($this);
        }

        return $this;
    }

    public function removeLike(CvLike $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getCv() === $this) {
                $like->setCv(null);
            }
        }

        return $this;
    }
}
