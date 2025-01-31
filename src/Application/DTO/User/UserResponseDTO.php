<?php

namespace App\Application\DTO\User;

use App\Domain\Model\User;
use OpenApi\Attributes as OA;

class UserResponseDTO
{
    #[OA\Property(type: "integer", example: 1)]
    public int $id;

    #[OA\Property(type: "string", format: "email", example: "john.doe@example.com")]
    public string $email;

    #[OA\Property(type: "string", example: "John", nullable: true)]
    public ?string $firstname;

    #[OA\Property(type: "string", example: "Doe", nullable: true)]
    public ?string $lastname;

    #[OA\Property(type: "string", example: "+33123456789", nullable: true)]
    public ?string $phoneNumber;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"), example: ["ROLE_USER"])]
    public array $roles;

    #[OA\Property(type: "string", format: "date-time", example: "2025-02-01 19:00:00")]
    public string $createdAt;

    #[OA\Property(type: "string", format: "date-time", example: "2025-02-01 19:00:00")]
    public string $updatedAt;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
        $this->phoneNumber = $user->getPhoneNumber();
        $this->roles = $user->getRoles();
        $this->createdAt = $user->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updatedAt = $user->getUpdatedAt()->format('Y-m-d H:i:s');
    }
}