<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CvLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CvLikeRepository::class)]
#[ApiResource]
class CvLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CV $cv = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recruiter = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCv(): ?CV
    {
        return $this->cv;
    }

    public function setCv(?CV $cv): static
    {
        $this->cv = $cv;

        return $this;
    }

    public function getRecruiter(): ?User
    {
        return $this->recruiter;
    }

    public function setRecruiter(?User $recruiter): static
    {
        $this->recruiter = $recruiter;

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
}
