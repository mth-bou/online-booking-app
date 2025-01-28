<?php

namespace App\Application\Service;

use App\Domain\Model\TimeSlotInterface;
use App\Domain\Repository\TimeSlotRepositoryInterface;
use App\Domain\Repository\RestaurantRepositoryInterface;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class TimeSlotService
{
    private TimeSlotRepositoryInterface $timeSlotRepository;
    private RestaurantRepositoryInterface $restaurantRepository;

    public function __construct(
        TimeSlotRepositoryInterface $timeSlotRepository,
        RestaurantRepositoryInterface $restaurantRepository
    ) {
        $this->timeSlotRepository = $timeSlotRepository;
        $this->restaurantRepository = $restaurantRepository;
    }

    public function addTimeSlot(
        int $restaurantId,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        bool $isAvailable
        ): TimeSlotInterface
    {
        $restaurant = $this->restaurantRepository->findById($restaurantId);

        if (!$restaurant) {
            throw new Exception("Restaurant not found.");
        }

        if ($startTime >= $endTime) {
            throw new Exception("Start time must be before end time.");
        }

        $timeSlot = $this->timeSlotRepository->createNew();
        $timeSlot->setRestaurant($restaurant);
        $timeSlot->setStartTime($startTime);
        $timeSlot->setEndTime($endTime);
        $timeSlot->setIsAvailable($isAvailable);

        $this->timeSlotRepository->save($timeSlot);

        return $timeSlot;
    }

    public function findTimeSlotById(int $timeSlotId): ?TimeSlotInterface
    {
        return $this->timeSlotRepository->findById($timeSlotId);
    }

    public function getAvailableTimeSlots(int $restaurantId): array
    {
        return $this->timeSlotRepository->findAvailableByRestaurant($restaurantId);
    }

    public function isTimeSlotAvailable(int $restaurantId, DateTimeImmutable $startTime, DateTimeImmutable $endTime): bool
    {
        return $this->timeSlotRepository->isTimeSlotAvailable($restaurantId, $startTime, $endTime);
    }

    public function updateTimeSlot(
        int $timeSlotId,
        ?DateTimeImmutable $startTime = null,
        ?DateTimeImmutable $endTime = null,
        ?bool $isAvailable = null
    ) : TimeSlotInterface {
        $timeSlot = $this->timeSlotRepository->findById($timeSlotId);

        if (!$timeSlot) {
            throw new NotFoundResourceException("Time slot not found.");
        }

        if ($startTime !== null) $timeSlot->setStartTime($startTime);
        if ($endTime !== null) $timeSlot->setEndTime($endTime);
        if ($isAvailable !== null) $timeSlot->setIsAvailable($isAvailable);

        $this->timeSlotRepository->save($timeSlot);

        return $timeSlot;
    }

    public function deleteTimeSlot(int $timeSlotId): void
    {
        $timeSlot = $this->timeSlotRepository->findById($timeSlotId);

        if (!$timeSlot) {
            throw new NotFoundResourceException("Time slot not found.");
        }

        $this->timeSlotRepository->delete($timeSlot);
    }
}