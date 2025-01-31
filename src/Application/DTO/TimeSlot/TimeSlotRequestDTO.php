<?php

namespace App\Application\DTO\TimeSlot;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
use DateTimeImmutable;

class TimeSlotRequestDTO
{
    #[OA\Property(type: "integer", example: 1)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $restaurantId;

    #[OA\Property(type: "string", format: "date-time", example: "2025-02-01 19:00:00")]
    #[Assert\NotBlank]
    #[Assert\Type(type: DateTimeImmutable::class, message: "Start time must be a valid DateTimeImmutable object.")]
    public DateTimeImmutable $startTime;

    #[OA\Property(type: "string", format: "date-time", example: "2025-02-01 21:00:00")]
    #[Assert\NotBlank]
    #[Assert\Type(type: DateTimeImmutable::class, message: "End time must be a valid DateTimeImmutable object.")]
    public DateTimeImmutable $endTime;

    #[OA\Property(type: "boolean", example: true)]
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