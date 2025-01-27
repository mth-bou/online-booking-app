<?php

namespace App\Application\Service;

use App\Domain\Model\TimeSlot;
use App\Domain\Repository\TimeSlotRepositoryInterface;
use App\Domain\Repository\RestaurantRepositoryInterface;
use DateTimeImmutable;
use Exception;

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

    public function addTimeSlot(int $restaurantId, DateTimeImmutable $startTime, DateTimeImmutable $endTime): TimeSlot
    {
        $restaurant = $this->restaurantRepository->findById($restaurantId);

        if (!$restaurant) {
            throw new Exception("Restaurant not found.");
        }

        $timeSlot = new TimeSlot();
        $timeSlot->setRestaurant($restaurant);
        $timeSlot->setStartTime($startTime);
        $timeSlot->setEndTime($endTime);

        $this->timeSlotRepository->save($timeSlot);

        return $timeSlot;
    }

    public function getAvailableTimeSlots(int $restaurantId): array
    {
        return $this->timeSlotRepository->findAvailableByRestaurant($restaurantId);
    }
}