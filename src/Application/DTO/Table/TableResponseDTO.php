<?php

namespace App\Application\DTO\Table;

use App\Domain\Model\Table;
use OpenApi\Attributes as OA;

class TableResponseDTO
{
    #[OA\Property(type: "integer", example: 1)]
    public int $id;

    #[OA\Property(type: "integer", example: 1)]
    public int $restaurantId;

    #[OA\Property(type: "integer", example: 4)]
    public int $capacity;

    #[OA\Property(type: "integer", example: 12)]
    public string $tableNumber;

    public function __construct(Table $table)
    {
        $this->id = $table->getId();
        $this->restaurantId = $table->getRestaurant()->getId();
        $this->capacity = $table->getCapacity();
        $this->tableNumber = $table->getTableNumber();
    }
}