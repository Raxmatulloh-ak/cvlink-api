<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Component\Salesforce\Dto\SalesforceCreateDto;
use App\Component\User\Dto\GoogleAuthDto;
use App\Component\User\Dto\TokenDto;
use App\Component\User\Dto\UserManageDto;
use App\Component\User\Dto\UserPreferencesDto;
use App\Controller\Salesforce\SalesforceCreateAction;
use App\Controller\User\UserAboutMeAction;
use App\Controller\User\UserAuthAction;
use App\Controller\User\UserCreateAction;
use App\Controller\User\UserGoogleAuthAction;
use App\Controller\User\UserManageAction;
use App\Controller\User\UserPreferencesAction;
use App\Controller\User\UserProfileAction;
use App\Controller\User\UserPublicProfileAction;
use App\Enum\Theme;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            security: "is_granted('ROLE_ADMIN') || object === user",
        ),
        new Post(
            controller: UserCreateAction::class,
            write: false,
        ),
        new Post(
            uriTemplate: '/users/auth',
            controller: UserAuthAction::class,
            output: TokenDto::class,
            write: false,
            name: 'userAuth',
        ),
        new Post(
            uriTemplate: '/users/about_me',
            controller: UserAboutMeAction::class,
            input: false,
            write: false,
            name: 'aboutMe',
        ),
        new Patch(
            uriTemplate: '/users/preferences',
            controller: UserPreferencesAction::class,
            input: UserPreferencesDto::class,
            read: false,
            write: false,
            name: 'user_preferences',
        ),
        new Patch(
            controller: UserManageAction::class,
            security: "is_granted('ROLE_ADMIN')",
            input: UserManageDto::class,
            write: false,
            name: 'user_manage',
        ),
        new Get(
            uriTemplate: '/users/{id}/profile',
            controller: UserProfileAction::class,
            security: "is_granted('ROLE_CANDIDATE') || is_granted('ROLE_ADMIN')",
            output: false,
            read: false,
            name: 'user_profile',
        ),
        new Get(
            uriTemplate: '/users/{id}/public-profile',
            controller: UserPublicProfileAction::class,
            output: false,
            read: false,
            name: 'user_public_profile',
        ),
        new Post(
            uriTemplate: '/users/auth/google',
            controller: UserGoogleAuthAction::class,
            input: GoogleAuthDto::class,
            output: TokenDto::class,
            read: false,
            write: false,
            name: 'userGoogleAuth',
        ),
        new Post(
            uriTemplate: '/users/{id}/salesforce',
            controller: SalesforceCreateAction::class,
            input: SalesforceCreateDto::class,
            output: false,
            read: false,
            write: false,
            name: 'user_salesforce_create',
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') || object === user",
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(enumType: UserStatus::class)]
    #[Groups(['user:read'])]
    private ?UserStatus $status = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = ['ROLE_CANDIDATE'];

    #[ORM\Column(length: 15)]
    #[Groups(['user:read', 'user:write'])]
    private string $locale = 'en';

    #[ORM\Column(enumType: Theme::class)]
    #[Groups(['user:read', 'user:write'])]
    private Theme $theme = Theme::Light;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 6, minMessage: 'Password must be at least {{ limit }} characters long')]
    #[Groups(['user:write'])]
    private ?string $password = null;

    /**
     * @var Collection<int, UserAuthIdentity>
     */
    #[ORM\OneToMany(
        targetEntity: UserAuthIdentity::class,
        mappedBy: 'owner',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $userAuthIdentities;

    /**
     * @var Collection<int, UserAttributeValue>
     */
    #[ORM\OneToMany(
        targetEntity: UserAttributeValue::class,
        mappedBy: 'owner',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $userAttributeValues;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'candidate', orphanRemoval: true)]
    private Collection $userProjects;

    /**
     * @var Collection<int, CV>
     */
    #[ORM\OneToMany(targetEntity: CV::class, mappedBy: 'candidate', cascade: ['persist'], orphanRemoval: true)]
    private Collection $cvs;

    public function __construct()
    {
        $this->userAuthIdentities = new ArrayCollection();
        $this->userAttributeValues = new ArrayCollection();
        $this->userProjects = new ArrayCollection();
        $this->cvs = new ArrayCollection();
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

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTheme(): Theme
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
        $this->email = strtolower(trim($email));

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
            $userAttributeValue->setOwner($this);
        }

        return $this;
    }

    public function removeUserAttributeValue(UserAttributeValue $userAttributeValue): static
    {
        if ($this->userAttributeValues->removeElement($userAttributeValue)) {
            // set the owning side to null (unless already changed)
            if ($userAttributeValue->getOwner() === $this) {
                $userAttributeValue->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getUserProjects(): Collection
    {
        return $this->userProjects;
    }

    public function addUserProject(Project $userProject): static
    {
        if (!$this->userProjects->contains($userProject)) {
            $this->userProjects->add($userProject);
            $userProject->setCandidate($this);
        }

        return $this;
    }

    public function removeUserProject(Project $userProject): static
    {
        if ($this->userProjects->removeElement($userProject)) {
            // set the owning side to null (unless already changed)
            if ($userProject->getCandidate() === $this) {
                $userProject->setCandidate(null);
            }
        }

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
            $cv->setCandidate($this);
        }

        return $this;
    }

    public function removeCv(CV $cv): static
    {
        if ($this->cvs->removeElement($cv)) {
            // set the owning side to null (unless already changed)
            if ($cv->getCandidate() === $this) {
                $cv->setCandidate(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
