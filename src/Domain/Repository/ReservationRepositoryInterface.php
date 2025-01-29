<?php

namespace App\Domain\Repository;

use App\Domain\Model\Reservation;

interface ReservationRepositoryInterface
{
    public function createNew(): Reservation;
    public function findById(int $id): ?Reservation;
    public function findByUser(int $userId): array;
    public function findByRestaurant(int $restaurantId): array;
    public function findByTable(int $tableId): array;
    public function findByTimeSlot(int $timeSlotId): array;
    public function findActiveReservations(): array;
    public function findPastReservations(): array;
    public function findCancelledReservations(): array;
    public function isTableAvailable(int $tableId, int $timeSlotId): bool;
    public function save(Reservation $reservation): void;
    public function delete(Reservation $reservation): void;
}
