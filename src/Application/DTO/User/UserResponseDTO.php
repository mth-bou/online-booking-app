<?php

namespace App\Application\DTO\User;

use App\Domain\Model\User;

class UserResponseDTO
{
    public int $id;
    public string $email;
    public ?string $firstname;
    public ?string $lastname;
    public ?string $phoneNumber;
    public array $roles;
    public string $createdAt;
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