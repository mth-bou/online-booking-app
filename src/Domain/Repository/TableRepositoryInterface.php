<?php

namespace App\Domain\Repository;

use App\Domain\Model\Table;
use DateTime;

interface TableRepositoryInterface
{
    public function findById(int $id): ?Table;
    public function findByRestaurant(int $restaurantId): array;
    public function findByMinimumCapacity(int $capacity): array;
    public function findAvailableTables(int $restaurantId, DateTime $startTime, DateTime $endTime, int $guests): array;
    public function findReservedTables(int $restaurantId, DateTime $startTime, DateTime $endTime): array;
    public function isTableAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool;
    public function save(Table $table): void;
    public function delete(Table $table): void;
}
