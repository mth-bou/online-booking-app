<?php

namespace App\Application\Port;

use App\Domain\Model\User;

interface UserUseCaseInterface
{
    public function createUser(
        string $email,
        string $password,
        ?string $firstname,
        ?string $lastname,
        ?string $phoneNumber
    ): User;

    public function authenticateUser(string $email, string $password): ?User;

    public function findUserById(int $userId): ?User;

    public function findUserByEmail(string $email): ?User;

    public function updateUser(int $userId, array $data): User;

    public function deleteUser(int $userId): void;

    public function getUserReservations(int $userId): array;

    public function getUserReviews(int $userId): array;

    public function getUserNotifications(int $userId): array;

    public function getUnreadNotifications(int $userId): array;

    public function markAllNotificationsAsRead(int $userId): void;
}