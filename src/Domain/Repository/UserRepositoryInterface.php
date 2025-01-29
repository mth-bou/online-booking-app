<?php

namespace App\Domain\Repository;

use App\Domain\Model\User;

interface UserRepositoryInterface
{
    public function createNew(): User;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByPhoneNumber(string $phoneNumber): ?User;
    public function searchByName(string $name): array;
    public function findAll(): array;
    public function emailExists(string $email): bool;
    public function save(User $user): void;
    public function delete(User $user): void;
}
