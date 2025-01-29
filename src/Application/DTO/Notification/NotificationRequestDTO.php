<?php

namespace App\Application\DTO\Notification;

use Symfony\Component\Validator\Constraints as Assert;

class NotificationRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public string $message;

    public function __construct(int $userId, string $message)
    {
        $this->userId = $userId;
        $this->message = $message;
    }
}