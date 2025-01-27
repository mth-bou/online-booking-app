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
}