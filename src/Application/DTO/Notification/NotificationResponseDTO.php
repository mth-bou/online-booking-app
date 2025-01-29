<?php

namespace App\Application\DTO\Notification;

use App\Domain\Model\Notification;

class NotificationResponseDTO
{
    public int $id;
    public int $userId;
    public string $message;
    public string $type;
    public string $status;
    public bool $isRead;
    public string $createdAt;
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