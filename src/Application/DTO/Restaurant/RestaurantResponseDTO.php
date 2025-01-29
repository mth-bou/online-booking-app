<?php

namespace App\Application\DTO\Restaurant;

use App\Domain\Model\Restaurant;
use App\Domain\Model\TimeSlot;

class RestaurantResponseDTO
{
    public int $id;
    public string $name;
    public string $type;
    public string $description;
    public string $address;
    public string $city;
    public string $postalCode;
    public string $phoneNumber;
    public array $tables = [];
    public array $reviews = [];
    public array $timeSlots = [];

    public function __construct(Restaurant $restaurant)
    {
        $this->id = $restaurant->getId();
        $this->name = $restaurant->getName();
        $this->type = $restaurant->getType();
        $this->description = $restaurant->getDescription();
        $this->address = $restaurant->getAddress();
        $this->city = $restaurant->getCity();
        $this->postalCode = $restaurant->getPostalCode();
        $this->phoneNumber = $restaurant->getPhoneNumber();

        foreach ($restaurant->getTables() as $table) {
            $this->tables[] = [
                'id' => $table->getId(),
                'capacity' => $table->getCapacity(),
            ];
        }

        foreach ($restaurant->getReviews() as $review) {
            $this->reviews[] = [
                'id' => $review->getId(),
                'rating' => $review->getRating(),
                'comment' => $review->getComment(),
            ];
        }

        foreach ($restaurant->getTimeSlots() as $timeSlot) {
            if ($timeSlot instanceof TimeSlot) {
                $this->timeSlots[] = [
                    'id' => $timeSlot->getId(),
                    'startTime' => $timeSlot->getStartTime()?->format('H:i') ?? 'N/A',
                    'endTime' => $timeSlot->getEndTime()?->format('H:i') ?? 'N/A',
                ];
            }
        }
    }
}