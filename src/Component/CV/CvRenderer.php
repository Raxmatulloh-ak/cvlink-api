<?php

declare(strict_types=1);

namespace App\Component\CV;

use App\Component\UserAttributeValue\UserAttributeValueService;
use App\Entity\CV;
use App\Entity\User;
use App\Entity\UserAttributeValue;
use App\Enum\AttributeValueType;
use App\Repository\CvLikeRepository;
use App\Repository\PositionAttributeRepository;
use App\Repository\PositionTagRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectTagRepository;
use App\Repository\UserAttributeValueRepository;

class CvRenderer
{
    public function __construct(
        private readonly PositionAttributeRepository $positionAttributeRepository,
        private readonly PositionTagRepository $positionTagRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly ProjectTagRepository $projectTagRepository,
        private readonly CvLikeRepository $cvLikeRepository,
        private readonly UserAttributeValueRepository $userAttributeValueRepository,
        private readonly UserAttributeValueService $userAttributeValueService,
    ) {
    }

    public function render(CV $cv, ?User $viewer = null): array
    {
        $candidate = $cv->getCandidate();
        $position = $cv->getPosition();

        if ($candidate === null || $position === null) {
            return [];
        }

        $values = [];

        foreach ($this->userAttributeValueRepository->findForOwner($candidate) as $value) {
            $attributeId = $value->getAttribute()?->getId();

            if ($attributeId !== null) {
                $values[$attributeId] = $value;
            }
        }

        $attributes = [];
        $complete = true;

        foreach ($this->positionAttributeRepository->findForPosition($position) as $positionAttribute) {
            $attribute = $positionAttribute->getAttribute();

            if ($attribute === null || $attribute->getId() === null) {
                continue;
            }

            $value = $values[$attribute->getId()] ?? null;
            $filled = $value !== null && $this->userAttributeValueService->isFilled($value);
            $complete = $complete && $filled;

            $attributes[] = [
                'attributeId' => $attribute->getId(),
                'name' => $attribute->getName(),
                'type' => $attribute->getValueType()?->value,
                'builtinKey' => $attribute->getBuiltinKey(),
                'valueId' => $value?->getId(),
                'version' => $value?->getVersion(),
                'empty' => !$filled,
                'value' => $value === null ? null : $this->getValue($value),
            ];
        }

        $projects = $this->getProjects($cv);
        $likes = $this->cvLikeRepository->countForCv($cv);
        $liked = $viewer !== null && $this->cvLikeRepository->isLikedBy($cv, $viewer);

        return [
            'id' => $cv->getId(),
            'candidateId' => $candidate->getId(),
            'positionId' => $position->getId(),
            'title' => $position->getTitle(),
            'status' => $cv->getStatus()?->value,
            'version' => $cv->getVersion(),
            'complete' => $complete,
            'createdAt' => $cv->getCreatedAt()?->format(DATE_ATOM),
            'publishedAt' => $cv->getPublishedAt()?->format(DATE_ATOM),
            'attributes' => $attributes,
            'projects' => $projects,
        ];
    }

    private function getValue(UserAttributeValue $value): mixed
    {
        return match ($value->getAttribute()?->getValueType()) {
            AttributeValueType::STRING,
            AttributeValueType::TEXT => $value->getTextValue(),
            AttributeValueType::IMAGE => $value->getImageReference(),
            AttributeValueType::NUMERIC => $value->getNumericValue(),
            AttributeValueType::DATE => $value->getDateValue()?->format('Y-m-d'),
            AttributeValueType::PERIOD => [
                'start' => $value->getPeriodStart()?->format('Y-m-d'),
                'end' => $value->getPeriodEnd()?->format('Y-m-d'),
            ],
            AttributeValueType::BOOLEAN => $value->isBooleanValue(),
            AttributeValueType::DROPDOWN => $value->getOption() === null ? null : [
                'id' => $value->getOption()?->getId(),
                'label' => $value->getOption()?->getLabel(),
            ],
            default => null,
        };
    }

    private function getProjects(CV $cv): array
    {
        $position = $cv->getPosition();
        $candidate = $cv->getCandidate();

        if ($position === null || $candidate === null || $position->getMaxProjects() <= 0) {
            return [];
        }

        $tagIds = $this->positionTagRepository->findTagIds($position);
        $projects = $this->projectRepository->findForCv(
            $candidate,
            $tagIds,
            $position->getMaxProjects(),
        );

        $tagsByProject = [];

        foreach ($this->projectTagRepository->findForProjects($projects) as $projectTag) {
            $projectId = $projectTag->getProject()?->getId();
            $tagName = $projectTag->getTag()?->getName();

            if ($projectId !== null && $tagName !== null) {
                $tagsByProject[$projectId][] = $tagName;
            }
        }

        $result = [];

        foreach ($projects as $project) {
            $result[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'startDate' => $project->getStartDate()?->format('Y-m-d'),
                'endDate' => $project->getEndDate()?->format('Y-m-d'),
                'description' => $project->getDescription(),
                'tags' => $tagsByProject[$project->getId()] ?? [],
            ];
        }

        return $result;
    }
}
