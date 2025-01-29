<?php

namespace App\Application\DTO\Notification;

use App\Domain\Model\Notification;
use OpenApi\Attributes as OA;

class NotificationResponseDTO
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;
    #[OA\Property(property: "userId", type: "integer", example: 1)]
    public int $userId;
    #[OA\Property(property: "message", type: "string", example: "Hello World")]
    public string $message;
    #[OA\Property(property: "type", type: "string", example: "info")]
    public string $type;
    #[OA\Property(property:"status", type: "string", example:"SENT")]
    public string $status;
    #[OA\Property(property:"isRead", type:"bool", example:"false")]
    public bool $isRead;
    #[OA\Property(property:"createdAt", type:"string", example:"H:i:s")]
    public string $createdAt;
    #[OA\Property(property:"createdAt", type:"string", example:"H:i:s")]
    public string $updatedAt;

    public function __construct(Notification $notification)
    {
        $this->id = $notification->getId();
        $this->userId = $notification->getUser()->getId();
        $this->message = $notification->getMessage();
        $this->type = $notification->getType();
        $this->status = $notification->getStatus();
        $this->isRead = $notification->getIsRead();
        $this->createdAt = $notification->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updatedAt = $notification->getUpdatedAt()->format('Y-m-d H:i:s');
    }
}