<?php

namespace App\Application\Service;

use DateTimeImmutable;
use App\Domain\Enum\StatusEnum;
use App\Domain\Model\Notification;
use App\Domain\Repository\UserRepositoryInterface;
use App\Application\Port\NotificationUseCaseInterface;
use App\Domain\Repository\NotificationRepositoryInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class NotificationService implements NotificationUseCaseInterface
{
    private NotificationRepositoryInterface $notificationRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository, UserRepositoryInterface $userRepository) {
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
    }

    public function sendNotification(int $userId, string $message, string $type): Notification
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new NotFoundResourceException("User not found.");
        }

        $notification = $this->notificationRepository->createNew();
        $notification->setUser($user);
        $notification->setMessage($message);
        $notification->setType($type);
        $notification->setStatus(StatusEnum::PENDING->value);
        $notification->setIsRead(false);

        $this->notificationRepository->save($notification);

        return $notification;
    }

    public function markNotificationAsSent(int $notificationId): void
    {
        $notification = $this->notificationRepository->findById($notificationId);
        if (!$notification) {
            throw new NotFoundResourceException("Notification not found.");
        }

        $notification->setStatus(StatusEnum::SENT->value);
        $notification->setUpdatedAt(new DateTimeImmutable());
        $this->notificationRepository->save($notification);
    }

    public function markNotificationAsRead(int $notificationId): void
    {
        $notification = $this->notificationRepository->findById($notificationId);
        if (!$notification) {
            throw new NotFoundResourceException("Notification not found.");
        }

        $notification->setIsRead(true);
        $notification->setUpdatedAt(new DateTimeImmutable());
        $this->notificationRepository->save($notification);
    }

    public function getNotificationById(int $notificationId): Notification
    {
        $notification = $this->notificationRepository->findById($notificationId);
        if (!$notification) {
            throw new NotFoundResourceException("Notification not found.");
        }

        return $notification;
    }
}