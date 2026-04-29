<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?array;
    public function findById(int $id): ?array;
    public function findAll(int $limit, int $offset): array;
    public function countAll(): int;
    public function emailExists(string $email): bool;
    public function create(string $name, string $email, string $hashedPassword, string $role): array;
}