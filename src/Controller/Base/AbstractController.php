<?php

declare(strict_types=1);

namespace App\Controller\Base;

use ApiPlatform\Validator\ValidatorInterface;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    protected function validate(object $data, array $context = []): void
    {
        $this->validator->validate($data, $context);
    }
}
