<?php

declare(strict_types=1);

namespace App\Controller\UserAttributeValue;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\UserAttributeValue\Dto\UserAttributeValueWriteDto;
use App\Component\UserAttributeValue\UserAttributeValueFactory;
use App\Component\UserAttributeValue\UserAttributeValueManager;
use App\Component\UserAttributeValue\UserAttributeValueService;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Entity\UserAttributeValue;
use App\Repository\AttributeDefinitionRepository;
use App\Repository\UserAttributeValueRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAttributeValueCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly AttributeDefinitionRepository $attributeDefinitionRepository,
        private readonly UserAttributeValueRepository $userAttributeValueRepository,
        private readonly UserAttributeValueFactory $userAttributeValueFactory,
        private readonly UserAttributeValueService $userAttributeValueService,
        private readonly UserAttributeValueManager $userAttributeValueManager,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(UserAttributeValueWriteDto $data, Request $request): UserAttributeValue
    {
        $this->validate($data);
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $owner = $user;
        $ownerId = $request->attributes->getInt('id');

        if ($ownerId > 0) {
            $owner = $this->userRepository->find($ownerId);

            if ($owner === null) {
                throw new NotFoundHttpException('User not found');
            }
        }

        $attribute = $this->attributeDefinitionRepository->find($data->getAttributeId());

        if ($attribute === null) {
            throw new BadRequestHttpException('Attribute not found');
        }

        if ($this->userAttributeValueRepository->findOneBy(['owner' => $owner, 'attribute' => $attribute]) !== null) {
            throw new ConflictHttpException('Attribute value already exists');
        }

        $value = $this->userAttributeValueFactory->create($owner, $attribute);
        $this->userAttributeValueService->fill($value, $data);
        $this->userAttributeValueManager->save($value, true);

        return $value;
    }
}
