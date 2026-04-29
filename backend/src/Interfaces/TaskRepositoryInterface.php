<?php

namespace App\Interfaces;

interface TaskRepositoryInterface
{
    public function findAllByUser(int $userId, array $filters, int $limit, int $offset): array;
    public function countByUser(int $userId, array $filters): int;
    public function findById(int $id): ?array;
    public function findByIdAndUser(int $id, int $userId): ?array;
    public function create(array $data): array;
    public function update(int $id, int $userId, array $data): array;
    public function delete(int $id): void;
}