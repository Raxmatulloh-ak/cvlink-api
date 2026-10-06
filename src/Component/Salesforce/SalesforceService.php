<?php

declare(strict_types=1);

namespace App\Component\Salesforce;

use App\Component\Salesforce\Dto\SalesforceCreateDto;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SalesforceService
{
    public function __construct(
        private readonly SalesforceClient $salesforceClient,
        private readonly SalesforceUserDataProvider $salesforceUserDataProvider,
    ) {
    }

    /**
     * @return array{accountId: string, contactId: string}
     */
    public function create(User $user, SalesforceCreateDto $data): array
    {
        $userData = $this->salesforceUserDataProvider->get($user);

        if ($userData->getLastName() === null) {
            throw new BadRequestHttpException(
                'Fill in the Last Name profile field before adding the user to Salesforce.',
            );
        }

        $accessToken = $this->salesforceClient->authenticate();
        $accountId = $this->salesforceClient->createAccount($accessToken, $data);
        $contactId = $this->salesforceClient->createContact(
            $accessToken,
            $userData,
            $data,
            $accountId,
        );

        return ['accountId' => $accountId, 'contactId' => $contactId,];
    }
}
