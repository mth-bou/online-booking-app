<?php

namespace App\Application\DTO\Table;

use Symfony\Component\Validator\Constraints as Assert;

class TableRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $restaurantId;

    #[Assert\NotBlank]
    #[Assert\Positive(message: "Capacity should be a positive number.")]
    public int $capacity;

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