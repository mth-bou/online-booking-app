<?php

namespace App\Application\Service;

use App\Domain\Model\Reservation;
use App\Domain\Repository\ReservationRepositoryInterface;
use App\Domain\Repository\TableRepositoryInterface;
use App\Domain\Repository\TimeSlotRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Exception;

class ReservationService
{
    private ReservationRepositoryInterface $reservationRepository;
    private TableRepositoryInterface $tableRepository;
    private TimeSlotRepositoryInterface $timeSlotRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        ReservationRepositoryInterface $reservationRepository,
        TableRepositoryInterface $tableRepository,
        TimeSlotRepositoryInterface $timeSlotRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->tableRepository = $tableRepository;
        $this->timeSlotRepository = $timeSlotRepository;
        $this->userRepository = $userRepository;
    }

    public function createReservation(int $userId, int $tableId, int $timeSlotId): Reservation
    {
        $user = $this->userRepository->findById($userId);
        $table = $this->tableRepository->findById($tableId);
        $timeSlot = $this->timeSlotRepository->findById($timeSlotId);

        if (!$user || !$table || !$timeSlot) {
            throw new Exception("Invalid user, table, or time slot");
        }

        if (!$this->tableRepository->findAvailableTables(
            $table->getRestaurant()->getId(),
            $timeSlot->getStartTime(),
            $timeSlot->getEndTime(),
            $table->getCapacity())
            ) {
            throw new Exception("Table is not available for the selected time slot");
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setTable($table);
        $reservation->setTimeSlot($timeSlot);
        $reservation->setStatus(Reservation::STATUS_PENDING);
        $reservation->setCreatedAt(new DateTimeImmutable());
        $reservation->setUpdatedAt(new DateTimeImmutable());

        $this->reservationRepository->save($reservation);

        return $reservation;
    }

    public function cancelReservation(int $reservationId): void
    {
        $reservation = $this->reservationRepository->findById($reservationId);

        if (!$reservation) {
            throw new Exception("Reservation not found.");
        }

        $reservation->setStatus(Reservation::STATUS_CANCELLED);
        $reservation->setUpdatedAt(new DateTimeImmutable());
        $this->reservationRepository->save($reservation);
    }

    public function confirmReservation(int $reservationId): void
    {
        $reservation = $this->reservationRepository->findById($reservationId);

        if (!$reservation) {
            throw new Exception("Reservation not found.");
        }

        $reservation->setStatus(Reservation::STATUS_CONFIRMED);
        $reservation->setUpdatedAt(new DateTimeImmutable());
        $this->reservationRepository->save($reservation);
    }

    public function completeReservation(int $reservationId): void
    {
        $reservation = $this->reservationRepository->findById($reservationId);

        if (!$reservation) {
            throw new Exception("Reservation not found.");
        }

        $reservation->setStatus(Reservation::STATUS_COMPLETED);
        $reservation->setUpdatedAt(new DateTimeImmutable());
        $this->reservationRepository->save($reservation);
    }

    public function rejectReservation(int $reservationId): void
    {
        $reservation = $this->reservationRepository->findById($reservationId);
        
        if (!$reservation) {
            throw new Exception("Reservation not found.");
        }

        $reservation->setStatus(Reservation::STATUS_REJECTED);
        $reservation->setUpdatedAt(new DateTimeImmutable());
        $this->reservationRepository->save($reservation);
    }

    public function getUserReservations(int $userId): array
    {
        return $this->reservationRepository->findByUser($userId);
    }

    public function getRestaurantReservations(int $restaurantId): array
    {
        return $this->reservationRepository->findByRestaurant($restaurantId);
    }

    public function getUpcomingReservations(int $userId): array
    {
        return array_filter(
            $this->reservationRepository->findByUser($userId),
            fn($reservation) => $reservation->getTimeSlot()->getStartTime() > new DateTimeImmutable()
        );
    }

    public function getPastReservations(int $userId): array
    {
        return array_filter(
            $this->reservationRepository->findByUser($userId),
            fn($reservation) => $reservation->getTimeSlot()->getEndTime() < new DateTimeImmutable()
        );
    }

    public function isTableAvailable(int $tableId, int $timeSlotId): bool
    {
        return $this->reservationRepository->isTableAvailable($tableId, $timeSlotId);
    }
}