<?php

namespace App\Repositories;

use App\Database;
use App\Interfaces\SubjectRepositoryInterface;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function findAllByUser(int $userId, int $limit, int $offset): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM subjects WHERE user_id = :uid
             ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':uid',    $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit',  $limit,  \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAll(int $limit, int $offset): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM subjects ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit',  $limit,  \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countByUser(int $userId): int
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) FROM subjects WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countAll(): int
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) FROM subjects');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM subjects WHERE id = ?');
        $stmt->execute([$id]);
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findByIdAndUser(int $id, int $userId): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM subjects WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(int $userId, string $name, ?string $description): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO subjects (user_id, name, description) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $name, $description]);

        $newId = $db->lastInsertId();
        return $this->findById((int) $newId);
    }

    public function update(int $id, int $userId, string $name, ?string $description): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'UPDATE subjects SET name = ?, description = ? WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$name, $description, $id, $userId]);
        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM subjects WHERE id = ?');
        $stmt->execute([$id]);
    }
}