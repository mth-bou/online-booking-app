<?php

namespace App\Application\Port;

use App\Domain\Model\Notification;

interface NotificationUseCaseInterface
{
    public function sendNotification(int $userId, string $message, string $type): Notification;

    public function markNotificationAsSent(int $notificationId): void;

    public function markNotificationAsRead(int $notificationId): void;

    public function getNotificationById(int $notificationId): Notification;
}