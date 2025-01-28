<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Enum\StatusEnum;
use \App\Domain\Model\Notification;
use App\Domain\Model\Interface\NotificationInterface;
use App\Domain\Repository\NotificationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use DateTime;
use DateTimeImmutable;

class NotificationRepository implements NotificationRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Notification::class);
    }

    public function createNew(): NotificationInterface
    {
        return new Notification();
    }

    public function findById(int $id): ?NotificationInterface
    {
        return $this->repository->find($id);
    }

    public function findByUser(int $userId): array
    {
        return $this->repository->findBy(["user"=> $userId], ['createdAt' => 'DESC']);
    }

    public function findUnreadByUser(int $userId): array
    {
        return $this->repository->findBy(['user' => $userId, 'isRead' => false], ['createdAt' => 'DESC']);
    }

    public function findByUserAndDateRange(int $userId, DateTime $startDate, DateTime $endDate): array
    {
        return $this->em->createQueryBuilder()
        ->select('n')
        ->from(Notification::class, 'n')
        ->where('n.user = :userId')
        ->andWhere('n.createdAt BETWEEN :startDate AND :endDate')
        ->setParameter('userId', $userId)
        ->setParameter('startDate', $startDate)
        ->setParameter('endDate', $endDate)
        ->orderBy('n.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function findByStatus(StatusEnum $status): array
    {
        return $this->repository->findBy(['status' => $status->value], ['createdAt' => 'DESC']);
    }

    public function findPendingNotifications(): array
    {
        return $this->findByStatus(StatusEnum::PENDING);
    }

    public function findFailedNotifications(): array
    {
        return $this->findByStatus(StatusEnum::FAILED);
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = $this->repository->find($notificationId);
        if ($notification instanceof NotificationInterface) {
            $notification->setIsRead(true);
            $notification->setupdatedAt(new DateTimeImmutable());
            $this->save($notification);
        }
    }

    public function markAllAsRead(int $userId): void
    {
        $this->em->createQueryBuilder()
        ->update(Notification::class, 'n')
        ->set('n.isRead', ':isRead')
        ->set('n.updatedAt', ':updatedAt')
        ->where('n.user = :userId')
        ->setParameter('isRead', true)
        ->setParameter('updatedAt', new DateTimeImmutable())
        ->setParameter('userId', $userId)
        ->getQuery()
        ->execute();
    }

    public function updateNotificationStatus(int $notificationId, string $status): void
    {
        $notification = $this->repository->find($notificationId);
        if ($notification instanceof NotificationInterface) {
            $notification->setStatus($status);
            $this->save($notification);
        }
    }

    public function save(NotificationInterface $notification): void
    {
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function delete(NotificationInterface $notification): void
    {
        $this->em->remove($notification);
        $this->em->flush();
    }

    public function deleteAllByUser(int $userId): void
    {
        $this->em->createQueryBuilder()
        ->delete(Notification::class, 'n')
        ->where('n.user = :userId')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->execute();
    }
}