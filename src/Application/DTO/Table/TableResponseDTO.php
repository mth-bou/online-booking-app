<?php

namespace App\Application\DTO\Table;

use App\Domain\Model\Table;

class TableResponseDTO
{
    public int $id;
    public int $restaurantId;
    public string $restaurantName;
    public int $capacity;
    public string $tableNumber;

    public function __construct(Table $table)
    {
        $this->id = $table->getId();
        $this->restaurantId = $table->getRestaurant()->getId();
        $this->restaurantName = $table->getRestaurant()->getName();
        $this->capacity = $table->getCapacity();
        $this->tableNumber = $table->getTableNumber();
    }
}