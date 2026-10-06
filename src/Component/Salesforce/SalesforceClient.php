<?php

declare(strict_types=1);

namespace App\Component\Salesforce;

use App\Component\Salesforce\Dto\SalesforceAccessTokenDto;
use App\Component\Salesforce\Dto\SalesforceCreateDto;
use App\Component\Salesforce\Dto\SalesforceUserDataDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SalesforceClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,

        #[Autowire('%env(SALESFORCE_LOGIN_URL)%')]
        private readonly string $loginUrl,

        #[Autowire('%env(SALESFORCE_CLIENT_ID)%')]
        private readonly string $clientId,

        #[Autowire('%env(SALESFORCE_CLIENT_SECRET)%')]
        private readonly string $clientSecret,

        #[Autowire('%env(SALESFORCE_API_VERSION)%')]
        private readonly string $apiVersion,
    ) {
    }

    public function authenticate(): SalesforceAccessTokenDto
    {
        $response = $this->httpClient->request('POST', rtrim($this->loginUrl, '/') . '/services/oauth2/token', [
            'body' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ],
        ]);

        $data = $response->toArray(false);

        if (isset($data['access_token'], $data['instance_url']) === false) {
            throw new HttpException(Response::HTTP_BAD_GATEWAY, 'Salesforce authentication failed.');
        }

        return new SalesforceAccessTokenDto($data['access_token'], $data['instance_url']);
    }

    public function createAccount(SalesforceAccessTokenDto $accessToken, SalesforceCreateDto $data): string
    {
        return $this->createRecord($accessToken, 'Account', ['Name' => $data->getCompanyName()]);
    }

    public function createContact(
        SalesforceAccessTokenDto $accessToken,
        SalesforceUserDataDto $userData,
        SalesforceCreateDto $data,
        string $accountId,
    ): string {
        return $this->createRecord($accessToken, 'Contact', [
            'AccountId' => $accountId,
            'FirstName' => $userData->getFirstName(),
            'LastName' => $userData->getLastName(),
            'Email' => $userData->getEmail(),
            'MailingCity' => $userData->getLocation(),
            'Title' => $data->getJobTitle(),
            'Phone' => $data->getPhone(),
        ]);
    }

    /**
     * @param array<string, string|null> $payload
     */
    private function createRecord(SalesforceAccessTokenDto $accessToken, string $object, array $payload): string
    {
        $url = sprintf(
            '%s/services/data/%s/sobjects/%s/',
            rtrim($accessToken->getInstanceUrl(), '/'),
            trim($this->apiVersion, '/'),
            $object,
        );

        $response = $this->httpClient->request(
            'POST',
            $url,
            ['auth_bearer' => $accessToken->getAccessToken(), 'json' => $payload]
        );

        $data = $response->toArray(false);

        if (isset($data['id']) === false) {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                sprintf('Unable to create Salesforce %s.', $object)
            );
        }

        return $data['id'];
    }
}
