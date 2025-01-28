<?php

namespace App\Domain\Repository;

use App\Domain\Model\Interface\NotificationInterface;
use DateTime;

interface NotificationRepositoryInterface
{
    public function createNew(): NotificationInterface;
    public function findById(int $id): ?NotificationInterface;
    public function findByUser(int $userId): array;
    public function findUnreadByUser(int $userId): array;
    public function findByUserAndDateRange(int $userId, DateTime $startDate, DateTime $endDate): array;
    public function markAsRead(int $notificationId): void;
    public function markAllAsRead(int $userId): void;
    public function save(NotificationInterface $notification): void;
    public function delete(NotificationInterface $notification): void;
    public function deleteAllByUser(int $userId): void;
}
