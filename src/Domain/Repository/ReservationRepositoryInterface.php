<?php

namespace App\Domain\Repository;

use App\Domain\Contract\ReservationInterface;

interface ReservationRepositoryInterface
{
    public function createNew(): ReservationInterface;
    public function findById(int $id): ?ReservationInterface;
    public function findByUser(int $userId): array;
    public function findByRestaurant(int $restaurantId): array;
    public function findByTable(int $tableId): array;
    public function findByTimeSlot(int $timeSlotId): array;
    public function findActiveReservations(): array;
    public function findPastReservations(): array;
    public function findCancelledReservations(): array;
    public function isTableAvailable(int $tableId, int $timeSlotId): bool;
    public function save(ReservationInterface $reservation): void;
    public function delete(ReservationInterface $reservation): void;
}
