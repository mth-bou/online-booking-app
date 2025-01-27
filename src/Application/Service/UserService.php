<?php

namespace App\Application\Service;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class UserService
{
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function createUser(
        string $email,
        string $password,
        ?string $firstname,
        ?string $lastname,
        ?string $phonNumber
        ): User
    {
        if ($this->userRepository->emailExists($email)) {
            throw new Exception("Email already in use.");
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPhoneNumber($phonNumber);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->userRepository->save($user);

        return $user;
    }

    public function authenticateUser(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return null;
        }

        return $user;
    }

    public function findUserById(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function updateUser(int $userId, array $data): User
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) throw new Exception("User not found.");

        if (isset($data['firstname'])) $user->setFirstname($data['firstname']);

        if (isset($data['lastname'])) $user->setLastname($data['lastname']);

        if (isset($data['phoneNumber'])) $user->setPhoneNumber($data['phoneNumber']);

        if (isset($data['password'])) $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        $this->userRepository->save($user);

        return $user;
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new NotFoundResourceException("User not found.");
        }

        $this->userRepository->delete($user);
    }
}