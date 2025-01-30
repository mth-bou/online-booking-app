<?php

namespace App\Application\DTO\Restaurant;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class RestaurantRequestDTO
{
    #[OA\Property(type: "string", example: "Le Gourmet")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $name;

    #[OA\Property(type: "string", example: "French gastronomy")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $type;

    #[OA\Property(type: "string", example: "A fine dining experience.")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 255)]
    public string $description;

    #[OA\Property(type: "string", example: "123 Main Street")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public string $address;

    #[OA\Property(type: "string", example: "Paris")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public string $city;

    #[OA\Property(type: "string", example: "75000")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 10)]
    #[Assert\Regex(pattern: "/^\d{5,10}$/", message: "Invalid postal code.")]
    public string $postalCode;

    #[OA\Property(type: "string", example: "+33123456789")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 20)]
    #[Assert\Regex(pattern: "/^\+?\d{10,20}$/", message: "Invalid phone number.")]
    public string $phoneNumber;

    public function __construct(
        string $name,
        string $type,
        string $description,
        string $address,
        string $city,
        string $postalCode,
        string $phoneNumber
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->address = $address;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->phoneNumber = $phoneNumber;
    }
}