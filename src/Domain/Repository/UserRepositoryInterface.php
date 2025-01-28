<?php

namespace App\Domain\Repository;

use App\Domain\Contract\UserModelInterface;

interface UserRepositoryInterface
{
    public function createNew(): UserModelInterface;
    public function findById(int $id): ?UserModelInterface;
    public function findByEmail(string $email): ?UserModelInterface;
    public function findByPhoneNumber(string $phoneNumber): ?UserModelInterface;
    public function searchByName(string $name): array;
    public function findAll(): array;
    public function emailExists(string $email): bool;
    public function save(UserModelInterface $user): void;
    public function delete(UserModelInterface $user): void;
}
