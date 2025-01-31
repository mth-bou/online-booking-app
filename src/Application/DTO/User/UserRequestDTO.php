<?php

namespace App\Application\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class UserRequestDTO
{
    #[OA\Property(type: "string", format: "email", example: "john.doe@example.com")]
    #[Assert\NotBlank]
    #[Assert\Email(message: "Invalid email format.")]
    public string $email;

    #[OA\Property(type: "string", example: "secret123", nullable: true)]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 6, max: 255, groups: ['create'], minMessage: "Password must be at least 6 characters long.")]
    public ?string $password = null;

    #[OA\Property(type: "string", example: "John", nullable: true)]
    #[Assert\Length(min: 2, max: 255)]
    public ?string $firstname = null;

    #[OA\Property(type: "string", example: "Doe", nullable: true)]
    #[Assert\Length(min: 2, max: 255)]
    public ?string $lastname = null;

    #[OA\Property(type: "string", example: "+33123456789", nullable: true)]
    #[Assert\Length(min: 10, max: 20)]
    #[Assert\Regex(pattern: "/^\+?[0-9]{10,20}$/", message: "Invalid phone number format.")]
    public ?string $phoneNumber = null;

    public function __construct(
        string $email,
        ?string $password,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $phoneNumber = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->phoneNumber = $phoneNumber;
    }
}