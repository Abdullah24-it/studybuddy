<?php

namespace App\Interfaces;

interface SubjectRepositoryInterface
{
    public function findAllByUser(int $userId, int $limit, int $offset): array;
    public function findAll(int $limit, int $offset): array;
    public function countByUser(int $userId): int;
    public function countAll(): int;
    public function findById(int $id): ?array;
    public function findByIdAndUser(int $id, int $userId): ?array;
    public function create(int $userId, string $name, ?string $description): array;
    public function update(int $id, int $userId, string $name, ?string $description): array;
    public function delete(int $id): void;
}