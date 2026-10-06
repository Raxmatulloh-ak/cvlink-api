<?php

declare(strict_types=1);

namespace App\Component\User;

use Exception;
use Google\Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GoogleIdTokenVerifier
{
    public function __construct(
        #[Autowire('%env(GOOGLE_CLIENT_ID)%')]
        private readonly string $clientId,
    ) {
    }

    public function verify(?string $credential): array
    {
        if ($credential === null || $credential === '') {
            throw new UnauthorizedHttpException('Bearer', 'Invalid Google credential');
        }

        try {
            $payload = new Client(['client_id' => $this->clientId])->verifyIdToken($credential);
        } catch (Exception) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid Google credential');
        }

        if (
            !is_array($payload)
            || !is_string($payload['sub'] ?? null)
            || !is_string($payload['email'] ?? null)
            || ($payload['email_verified'] ?? false) !== true
        ) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid Google credential');
        }

        return ['sub' => $payload['sub'], 'email' => $payload['email'],];
    }
}
