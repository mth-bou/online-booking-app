<?php

namespace App\Application\DTO\Notification;

use App\Domain\Enum\StatusEnum;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class NotificationRequestDTO
{
    #[OA\Property(property: "userId", type: "integer", example: 1)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[OA\Property(property: "message", type: "string", example: "Hello World")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public string $message;

    #[OA\Property(property: "type", type: "string", example: "info")]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $type;

    #[OA\Property(property:"status", type: "string", example:"SENT")]
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