<?php

declare(strict_types=1);

namespace App\Controller\UserAttributeValue;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\UserAttributeValue\Dto\UserAttributeValueUpdateDto;
use App\Component\UserAttributeValue\UserAttributeValueManager;
use App\Component\UserAttributeValue\UserAttributeValueService;
use App\Controller\Base\AbstractController;
use App\Entity\UserAttributeValue;
use App\Repository\UserAttributeValueRepository;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAttributeValueUpdateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly UserAttributeValueRepository $userAttributeValueRepository,
        private readonly UserAttributeValueService $userAttributeValueService,
        private readonly UserAttributeValueManager $userAttributeValueManager,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(UserAttributeValueUpdateDto $data, Request $request): UserAttributeValue
    {
        $this->validate($data);
        $value = $this->userAttributeValueRepository->find($request->attributes->getInt('id'));

        if ($value === null) {
            throw new NotFoundHttpException('Attribute value not found');
        }

        if ($value->getVersion() !== $data->getVersion()) {
            throw new ConflictHttpException('Attribute value was modified');
        }

        $this->userAttributeValueService->fill($value, $data);

        try {
            $this->userAttributeValueManager->save($value, true);
        } catch (OptimisticLockException $exception) {
            throw new ConflictHttpException('Attribute value was modified', $exception);
        }

        return $value;
    }
}
