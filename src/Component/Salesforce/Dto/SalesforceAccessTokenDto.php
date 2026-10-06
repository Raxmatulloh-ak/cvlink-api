<?php

declare(strict_types=1);

namespace App\Component\Salesforce\Dto;

class SalesforceAccessTokenDto
{
    public function __construct(
        private readonly string $accessToken,
        private readonly string $instanceUrl,
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getInstanceUrl(): string
    {
        return $this->instanceUrl;
    }
}
