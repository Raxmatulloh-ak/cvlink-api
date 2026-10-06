<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Component\User\Dto\GoogleAuthDto;
use App\Component\User\Dto\TokenDto;
use App\Component\User\GoogleIdTokenVerifier;
use App\Component\User\TokenCreator;
use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Component\UserAuthIdentity\UserAuthIdentityFactory;
use App\Component\UserAuthIdentity\UserAuthIdentityManager;
use App\Controller\Base\AbstractController;
use App\Enum\UserStatus;
use App\Repository\UserAuthIdentityRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserGoogleAuthAction extends AbstractController
{
    public function __invoke(
        GoogleAuthDto $data,
        GoogleIdTokenVerifier $verifier,
        UserAuthIdentityRepository $identityRepository,
        UserRepository $userRepository,
        UserFactory $userFactory,
        UserManager $userManager,
        UserAuthIdentityFactory $identityFactory,
        UserAuthIdentityManager $identityManager,
        TokenCreator $tokenCreator,
    ): TokenDto {
        $google = $verifier->verify($data->getCredential());

        $identity = $identityRepository->findOneBy([
            'provider' => 'google',
            'providerSubject' => $google['sub'],
        ]);

        if ($identity !== null) {
            $user = $identity->getOwner();

            if ($user->getStatus() !== UserStatus::ACTIVE) {
                throw new UnauthorizedHttpException('Bearer', 'User account is blocked');
            }

            return $tokenCreator->create($user);
        }

        $user = $userRepository->findOneByEmail($google['email']);

        if ($user === null) {
            $user = $userFactory->createSocial($google['email']);
            $userManager->save($user);
        }

        if ($user->getStatus() !== UserStatus::ACTIVE) {
            throw new UnauthorizedHttpException('Bearer', 'User account is blocked');
        }

        $identity = $identityFactory->create(
            $user,
            'google',
            $google['sub'],
        );

        $identityManager->save($identity, true);

        return $tokenCreator->create($user);
    }
}
