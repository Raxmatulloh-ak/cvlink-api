<?php

declare(strict_types=1);

namespace App\Controller\Salesforce;

use ApiPlatform\Validator\ValidatorInterface;
use App\Component\Salesforce\Dto\SalesforceCreateDto;
use App\Component\Salesforce\SalesforceService;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SalesforceCreateAction extends AbstractController
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly UserRepository $userRepository,
        private readonly SalesforceService $salesforceService,
    ) {
        parent::__construct($validator);
    }

    public function __invoke(int $id, SalesforceCreateDto $data): JsonResponse
    {
        $this->validate($data);
        $owner = $this->userRepository->find($id);

        if ($owner === null) {
            throw new NotFoundHttpException('User not found');
        }

        $user = $this->getUser();

        if (!$user instanceof User || ($owner !== $user && !$this->isGranted('ROLE_ADMIN'))) {
            throw new AccessDeniedHttpException();
        }

        $result = $this->salesforceService->create($owner, $data);

        return new JsonResponse([
            'accountId' => $result['accountId'],
            'contactId' => $result['contactId'],
        ], Response::HTTP_CREATED);
    }
}
