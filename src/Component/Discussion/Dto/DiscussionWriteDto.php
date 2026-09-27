<?php

declare(strict_types=1);

namespace App\Component\Discussion\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class DiscussionWriteDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 20000)]
    private ?string $message = null;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
