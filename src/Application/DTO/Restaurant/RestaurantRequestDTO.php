<?php

namespace App\Application\DTO\Restaurant;

use Symfony\Component\Validator\Constraints as Assert;

class RestaurantRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $type;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 255)]
    public string $description;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public string $address;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public string $city;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 10)]
    #[Assert\Regex(pattern: "/^\d{5,10}$/", message: "Invalid postal code.")]
    public string $postalCode;

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