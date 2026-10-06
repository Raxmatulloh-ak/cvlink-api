<?php

declare(strict_types=1);

namespace App\Component\User\Dto;

use App\Enum\UserStatus;
use Doctrine\ORM\Mapping\GeneratedValue;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserManageDto
{
    /**
     * @var string[]|null
     */
    #[Assert\All([
        new Assert\Choice(choices: [
            'ROLE_CANDIDATE',
            'ROLE_RECRUITER',
            'ROLE_ADMIN',
        ]),
    ])]
    #[Groups(['user:write'])]
    private ?array $roles = null;

    #[Groups(['user:write'])]
    private ?UserStatus $status = null;

    /**
     * @return string[]|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param string[]|null $roles
     */
    public function setRoles(?array $roles): void
    {
        $this->roles = $roles;
    }

    public function getStatus(): ?UserStatus
    {
        return $this->status;
    }

    public function setStatus(?UserStatus $status): void
    {
        $this->status = $status;
    }
}
