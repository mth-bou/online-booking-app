<?php

namespace App\Application\DTO\Notification;

use App\Domain\Enum\StatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

class NotificationRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public string $message;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $type;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [StatusEnum::class, 'casesAsArray'], message: "Invalid status.")]
    public string $status;

    public function __construct(int $userId, string $message, string $type, string $status = StatusEnum::PENDING->value)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->type = $type;
        $this->status = $status;
    }
}