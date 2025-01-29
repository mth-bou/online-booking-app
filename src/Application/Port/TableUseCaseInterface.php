<?php

namespace App\Application\Port;

use App\Domain\Model\Table;
use DateTime;

interface TableUseCaseInterface
{
    public function addTable(int $restaurantId, int $capacity, string $tableNumber): Table;

    public function isTableAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool;
}