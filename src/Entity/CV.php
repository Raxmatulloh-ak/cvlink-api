<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\CvStatus;
use App\Repository\CVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CVRepository::class)]
#[ApiResource]
class CV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cvs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'cvs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position = null;

    #[ORM\Column(enumType: CvStatus::class)]
    private ?CvStatus $status = null;

    #[ORM\Column]
    private ?int $version = null;

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
