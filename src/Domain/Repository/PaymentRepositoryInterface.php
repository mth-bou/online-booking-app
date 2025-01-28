<?php

namespace App\Domain\Repository;

use App\Domain\Enum\StatusEnum;
use App\Domain\Contract\PaymentInterface;
use DateTime;

interface PaymentRepositoryInterface
{
    public function createNew(): PaymentInterface;
    public function findById(int $id): ?PaymentInterface;
    public function findByReservation(int $reservationId): array;
    public function findByUser(int $userId): array;
    public function findByRestaurant(int $restaurantId): array;
    public function findByStatus(StatusEnum $status): array;
    public function findByDateRange(DateTime $startDate, DateTime $endDate): array;
    public function save(PaymentInterface $payment): void;
    public function delete(PaymentInterface $payment): void;
}
