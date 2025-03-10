<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Enum\StatusEnum;
use App\Domain\Model\Reservation;
use App\Domain\Repository\ReservationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use DateTime;

class ReservationRepository implements ReservationRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Reservation::class);
    }

    public function createNew(): Reservation
    {
        return new Reservation();
    }

    public function findById(int $id): ?Reservation
    {
        return $this->repository->find($id);
    }

    public function findByUser(int $userId): array
    {
        return $this->repository->findBy(['user' => $userId]);
    }

    public function findByRestaurant(int $restaurantId): array
    {
        return $this->em->createQueryBuilder()
        ->select('r')
        ->from(Reservation::class, 'r')
        ->join('r.restaurantTable', 't')
        ->join('t.restaurant', 'rest')
        ->where('rest.id = :restaurantId')
        ->setParameter('restaurantId', $restaurantId)
        ->getQuery()
        ->getResult();
    }

    public function findByTable(int $tableId): array
    {
        return $this->repository->findBy(['restaurantTable' => $tableId]);
    }

    public function findByTimeSlot(int $timeSlotId): array
    {
        return $this->repository->findBy(['timeSlot'=> $timeSlotId]);
    }

    public function findActiveReservations(): array
    {
        return $this->repository->findBy([
            'status' => [StatusEnum::PENDING->value, StatusEnum::CONFIRMED->value]
        ]);
    }

    public function findPastReservations(): array
    {
        return $this->em->createQueryBuilder()
        ->select('r')
        ->from(Reservation::class,'r')
        ->where('r.timeSlot.endTime < :now')
        ->setParameter('now', new DateTime())
        ->getQuery()
        ->getResult();
    }

    public function findCancelledReservations(): array
    {
        return $this->repository->findBy(['status' => StatusEnum::CANCELED->value]);
    }

    public function isTableAvailable(int $tableId, int $timeSlotId): bool
    {
        $count = $this->em->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(Reservation::class, 'r')
            ->where('r.restaurantTable = :tableId')
            ->andWhere('r.timeSlot = :timeSlotId')
            ->setParameter('tableId', $tableId)
            ->setParameter('timeSlotId', $timeSlotId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count == 0;
    }

    public function save(Reservation $reservation): void
    {
        $this->em->persist($reservation);
        $this->em->flush();
    }

    public function delete(Reservation $reservation): void
    {
        $this->em->remove($reservation);
        $this->em->flush();
    }
}