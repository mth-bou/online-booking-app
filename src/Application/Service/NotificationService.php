<?php

namespace App\Application\Service;

use App\Application\Port\NotificationUseCaseInterface;
use App\Domain\Enum\StatusEnum;
use App\Domain\Model\Notification;
use App\Domain\Repository\NotificationRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class NotificationService implements NotificationUseCaseInterface
{
    private NotificationRepositoryInterface $notificationRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository, UserRepositoryInterface $userRepository) {
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
    }

    public function sendNotification(int $userId, string $message): Notification
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new NotFoundResourceException("User not found.");
        }

        $notification = $this->notificationRepository->createNew();
        $notification->setUser($user);
        $notification->setMessage($message);
        $notification->setStatus(StatusEnum::PENDING->value);

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
        $this->notificationRepository->save($notification);
    }

    public function markNotificationAsRead(int $notificationId): void
    {
        $notification = $this->notificationRepository->findById($notificationId);
        if (!$notification) {
            throw new NotFoundResourceException("Notification not found.");
        }

        $notification->setIsRead(true);
        $this->notificationRepository->save($notification);
    }
}