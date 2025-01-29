<?php

namespace App\Application\Port;

use App\Domain\Model\Reservation;

interface ReservationUseCaseInterface
{
    public function createReservation(int $userId, int $tableId, int $timeSlotId, ?string $status = null): Reservation;

    public function cancelReservation(int $reservationId): void;

    public function confirmReservation(int $reservationId): void;

    public function completeReservation(int $reservationId): void;

    public function rejectReservation(int $reservationId): void;

    public function getUserReservations(int $userId): array;

    public function getRestaurantReservations(int $restaurantId): array;

    public function getUpcomingReservations(int $userId): array;

    public function getPastReservations(int $userId): array;

    public function isTableAvailable(int $tableId, int $timeSlotId): bool;
}