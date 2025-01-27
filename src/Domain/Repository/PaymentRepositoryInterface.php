<?php

namespace App\Domain\Repository;

use App\Domain\Model\Payment;
use DateTime;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;
    public function findByReservation(int $reservationId): array;
    public function findByUser(int $userId): array;
    public function findByRestaurant(int $restaurantId): array;
    public function findByStatus(string $status): array;
    public function findByDateRange(DateTime $startDate, DateTime $endDate): array;
    public function save(Payment $payment): void;
    public function delete(Payment $payment): void;
}
