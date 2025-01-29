<?php

namespace App\Application\DTO\Notification;

use App\Domain\Model\Notification;

class NotificationResponseDTO
{
    public int $id;
    public int $userId;
    public string $type;
    public string $message;
    public string $status;
    public bool $isRead;

    public function __construct(Notification $notification)
    {
        $this->id = $notification->getId();
        $this->userId = $notification->getUser()->getId();
        $this->type = $notification->getType();
        $this->message = $notification->getMessage();
        $this->status = $notification->getStatus();
        $this->isRead = $notification->getIsRead();
    }
}