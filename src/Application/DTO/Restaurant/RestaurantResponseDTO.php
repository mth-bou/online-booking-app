<?php

namespace App\Application\DTO\Restaurant;

use App\Domain\Model\Restaurant;
use App\Domain\Model\TimeSlot;
use OpenApi\Attributes as OA;

class RestaurantResponseDTO
{
    #[OA\Property(type: "integer", example: 1)]
    public int $id;

    #[OA\Property(type: "string", example: "Le Gourmet")]
    public string $name;

    #[OA\Property(type: "string", example: "French gastronomy")]
    public string $type;

    #[OA\Property(type: "string", example: "A fine dining experience.")]
    public string $description;

    #[OA\Property(type: "string", example: "123 Main Street")]
    public string $address;

    #[OA\Property(type: "string", example: "Paris")]
    public string $city;

    #[OA\Property(type: "string", example: "75000")]
    public string $postalCode;

    #[OA\Property(type: "string", example: "+33123456789")]
    public string $phoneNumber;

    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 101),
                new OA\Property(property: "capacity", type: "integer", example: 4),
                new OA\Property(property: "tableNumber", type: "string", example: "507")
            ]
        )
    )]
    public array $tables = [];

    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 201),
                new OA\Property(property: "userId", type: "integer", example: 3),
                new OA\Property(property: "firstname", type: "string", example: "John"),
                new OA\Property(property: "lastname", type: "string", example: "Doe"),
                new OA\Property(property: "rating", type: "integer", example: 5),
                new OA\Property(property: "comment", type: "string", example: "Amazing food and service!")
            ]
        )
    )]
    public array $reviews = [];

    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 301),
                new OA\Property(property: "restaurantName", type: "string", example: "Le Gourmet"),
                new OA\Property(property: "startTime", type: "string", example: "12:00"),
                new OA\Property(property: "endTime", type: "string", example: "14:00"),
                new OA\Property(property: "isAvailable", type: "bool", example: true)
            ]
        )
    )]
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