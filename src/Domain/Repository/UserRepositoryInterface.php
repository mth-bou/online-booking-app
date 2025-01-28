<?php

namespace App\Domain\Repository;

use App\Domain\Model\Interface\UserInterface;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserInterface;
    public function findByEmail(string $email): ?UserInterface;
    public function findByPhoneNumber(string $phoneNumber): ?UserInterface;
    public function searchByName(string $name): array;
    public function findAll(): array;
    public function emailExists(string $email): bool;
    public function save(UserInterface $user): void;
    public function delete(UserInterface $user): void;
}
