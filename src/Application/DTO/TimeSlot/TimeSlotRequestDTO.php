<?php

namespace App\Application\DTO\TimeSlot;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

class TimeSlotRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $restaurantId;

    #[Assert\NotBlank]
    #[Assert\Type(type: DateTimeImmutable::class, message: "Start time must be a valid DateTimeImmutable object.")]
    public DateTimeImmutable $startTime;

    #[Assert\NotBlank]
    #[Assert\Type(type: DateTimeImmutable::class, message: "End time must be a valid DateTimeImmutable object.")]
    public DateTimeImmutable $endTime;

    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    public bool $isAvailable;

    public function __construct(int $restaurantId, DateTimeImmutable $startTime, DateTimeImmutable $endTime, bool $isAvailable)
    {
        $this->restaurantId = $restaurantId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->isAvailable = $isAvailable;
    }
}