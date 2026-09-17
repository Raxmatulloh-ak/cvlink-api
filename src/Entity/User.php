<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\Theme;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: UserStatus::class)]
    private ?UserStatus $status = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 15)]
    private ?string $locale = null;

    #[ORM\Column(enumType: Theme::class)]
    private ?Theme $theme = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    /**
     * @var Collection<int, UserAuthIdentity>
     */
    #[ORM\OneToMany(targetEntity: UserAuthIdentity::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $userAuthIdentities;

    public function __construct()
    {
        $this->userAuthIdentities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, UserAuthIdentity>
     */
    public function getUserAuthIdentities(): Collection
    {
        return $this->userAuthIdentities;
    }

    public function addUserAuthIdentity(UserAuthIdentity $userAuthIdentity): static
    {
        if (!$this->userAuthIdentities->contains($userAuthIdentity)) {
            $this->userAuthIdentities->add($userAuthIdentity);
            $userAuthIdentity->setOwner($this);
        }

        return $this;
    }

    public function removeUserAuthIdentity(UserAuthIdentity $userAuthIdentity): static
    {
        if ($this->userAuthIdentities->removeElement($userAuthIdentity)) {
            // set the owning side to null (unless already changed)
            if ($userAuthIdentity->getOwner() === $this) {
                $userAuthIdentity->setOwner(null);
            }
        }

        return $this;
    }
}
