<?php

declare(strict_types=1);

namespace App\Controller\User;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\User\Dto\UserPreferencesDto;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserPreferencesAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly UserManager $userManager,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(UserPreferencesDto $data): User
    {
        $this->validate($data);
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        if ($data->getLocale() !== null) {
            $user->setLocale($data->getLocale());
        }

        if ($data->getTheme() !== null) {
            $user->setTheme($data->getTheme());
        }

        $this->userManager->save($user, true);

        return $user;
    }
}
