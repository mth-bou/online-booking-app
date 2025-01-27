<?php

namespace App\Domain\Repository;

use App\Domain\Model\Notification;
use DateTime;

interface NotificationRepositoryInterface
{
    public function findById(int $id): ?Notification;
    public function findByUser(int $userId): array;
    public function findUnreadByUser(int $userId): array;
    public function findByUserAndDateRange(int $userId, DateTime $startDate, DateTime $endDate): array;
    public function markAsRead(int $notificationId): void;
    public function markAllAsRead(int $userId): void;
    public function save(Notification $notification): void;
    public function delete(Notification $notification): void;
    public function deleteAllByUser(int $userId): void;
}
