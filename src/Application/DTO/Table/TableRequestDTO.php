<?php

namespace App\Application\DTO\Table;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class TableRequestDTO
{
    #[OA\Property(type: "integer", example: 1)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $restaurantId;

    #[OA\Property(type: "integer", example: 4)]
    #[Assert\NotBlank]
    #[Assert\Positive(message: "Capacity should be a positive number.")]
    public int $capacity;

    #[OA\Property(type: "integer", example: 27)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 10)]
    public string $tableNumber;

    public function __construct(int $restaurantId, int $capacity, string $tableNumber)
    {
        $this->restaurantId = $restaurantId;
        $this->capacity = $capacity;
        $this->tableNumber = $tableNumber;
    }
}