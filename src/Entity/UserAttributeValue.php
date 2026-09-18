<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserAttributeValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAttributeValueRepository::class)]
#[ApiResource]
class UserAttributeValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userAttributeValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'userAttributeValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AttributeDefinition $attribute = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textValue = null;

    #[ORM\Column(length: 2024, nullable: true)]
    private ?string $imageReference = null;

    #[ORM\Column(nullable: true)]
    private ?int $nemericValue = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateValue = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $periodStart = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $periodEnd = null;

    #[ORM\Column(nullable: true)]
    private ?bool $boolenValue = null;

    #[ORM\ManyToOne]
    private ?AttributeOption $option = null;

    #[ORM\Column]
    private ?int $version = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAttribute(): ?AttributeDefinition
    {
        return $this->attribute;
    }

    public function setAttribute(?AttributeDefinition $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    public function setTextValue(?string $textValue): static
    {
        $this->textValue = $textValue;

        return $this;
    }

    public function getImageReference(): ?string
    {
        return $this->imageReference;
    }

    public function setImageReference(?string $imageReference): static
    {
        $this->imageReference = $imageReference;

        return $this;
    }

    public function getNemericValue(): ?int
    {
        return $this->nemericValue;
    }

    public function setNemericValue(?int $nemericValue): static
    {
        $this->nemericValue = $nemericValue;

        return $this;
    }

    public function getDateValue(): ?\DateTimeImmutable
    {
        return $this->dateValue;
    }

    public function setDateValue(?\DateTimeImmutable $dateValue): static
    {
        $this->dateValue = $dateValue;

        return $this;
    }

    public function getPeriodStart(): ?\DateTimeImmutable
    {
        return $this->periodStart;
    }

    public function setPeriodStart(?\DateTimeImmutable $periodStart): static
    {
        $this->periodStart = $periodStart;

        return $this;
    }

    public function getPeriodEnd(): ?\DateTimeImmutable
    {
        return $this->periodEnd;
    }

    public function setPeriodEnd(?\DateTimeImmutable $periodEnd): static
    {
        $this->periodEnd = $periodEnd;

        return $this;
    }

    public function isBoolenValue(): ?bool
    {
        return $this->boolenValue;
    }

    public function setBoolenValue(?bool $boolenValue): static
    {
        $this->boolenValue = $boolenValue;

        return $this;
    }

    public function getOption(): ?AttributeOption
    {
        return $this->option;
    }

    public function setOption(?AttributeOption $option): static
    {
        $this->option = $option;

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
}
