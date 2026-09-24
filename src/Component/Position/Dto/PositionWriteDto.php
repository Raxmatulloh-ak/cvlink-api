<?php

declare(strict_types=1);

namespace App\Component\Position\Dto;

use App\Enum\PositionAccessType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PositionWriteDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['position:write'])]
    private ?string $title = null;

    #[Groups(['position:write'])]
    private ?string $description = null;

    #[Assert\NotNull]
    #[Groups(['position:write'])]
    private ?PositionAccessType $accessType = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Groups(['position:write'])]
    private ?int $maxProjects = null;

    /**
     * @var PositionAttributeDto[]
     */
    #[Assert\Valid]
    #[Groups(['position:write'])]
    private array $attributes = [];

    /**
     * @var PositionAccessRuleDto[]
     */
    #[Assert\Valid]
    #[Groups(['position:write'])]
    private array $accessRules = [];

    /**
     * @var string[]
     */
    #[Assert\All([
        new Assert\Type(type: 'string'),
        new Assert\NotBlank(),
        new Assert\Length(max: 255),
    ])]
    #[Groups(['position:write'])]
    private array $tags = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title === null ? null : trim($title);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getAccessType(): ?PositionAccessType
    {
        return $this->accessType;
    }

    public function setAccessType(?PositionAccessType $accessType): void
    {
        $this->accessType = $accessType;
    }

    public function getMaxProjects(): ?int
    {
        return $this->maxProjects;
    }

    public function setMaxProjects(?int $maxProjects): void
    {
        $this->maxProjects = $maxProjects;
    }

    /**
     * @return PositionAttributeDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param PositionAttributeDto[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return PositionAccessRuleDto[]
     */
    public function getAccessRules(): array
    {
        return $this->accessRules;
    }

    /**
     * @param PositionAccessRuleDto[] $accessRules
     */
    public function setAccessRules(array $accessRules): void
    {
        $this->accessRules = $accessRules;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
