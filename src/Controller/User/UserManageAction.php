<?php

declare(strict_types=1);

namespace App\Controller\User;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\User\Dto\UserManageDto;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserManageAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly UserRepository $userRepository,
        private readonly UserManager $userManager,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(UserManageDto $data, Request $request): User
    {
        $this->validate($data);
        $user = $this->userRepository->find($request->attributes->getInt('id'));

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        if ($data->getRoles() !== null) {
            $user->setRoles(array_values(array_unique($data->getRoles())));
        }

        if ($data->getStatus() !== null) {
            $user->setStatus($data->getStatus());
        }

        $this->userManager->save($user, true);

        return $user;
    }
}
