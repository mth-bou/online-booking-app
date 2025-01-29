<?php

namespace App\Application\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Email(message: "Invalid email format.")]
    public string $email;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 6, max: 255, groups: ['create'], minMessage: "Password must be at least 6 characters long.")]
    public ?string $password = null;

    #[Assert\Length(min: 2, max: 255)]
    public ?string $firstname = null;

    #[Assert\Length(min: 2, max: 255)]
    public ?string $lastname = null;

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