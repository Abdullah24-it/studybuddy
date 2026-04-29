<?php

namespace App\Repositories;

use App\Database;
use App\Interfaces\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public function findAllByUser(int $userId, array $filters, int $limit, int $offset): array
    {
        $db     = Database::getConnection();
        [$whereSQL, $params] = $this->buildWhere($userId, $filters);

        $fetchParams   = $params;
        $fetchParams[] = $limit;
        $fetchParams[] = $offset;

        $stmt = $db->prepare("
            SELECT t.*, s.name AS subject_name
            FROM tasks t
            JOIN subjects s ON t.subject_id = s.id
            WHERE {$whereSQL}
            ORDER BY t.deadline ASC, t.priority DESC
            LIMIT ? OFFSET ?
        ");

        foreach ($fetchParams as $i => $val) {
            $type = is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue($i + 1, $val, $type);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countByUser(int $userId, array $filters): int
    {
        $db     = Database::getConnection();
        [$whereSQL, $params] = $this->buildWhere($userId, $filters);

        $stmt = $db->prepare("SELECT COUNT(*) FROM tasks t WHERE {$whereSQL}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findByIdAndUser(int $id, int $userId): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO tasks (user_id, subject_id, title, description, deadline, priority, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['user_id'],
            $data['subject_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['deadline']    ?? null,
            $data['priority']    ?? 'medium',
            'pending',
        ]);

        return $this->findById((int) $db->lastInsertId());
    }

    public function update(int $id, int $userId, array $data): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE tasks
            SET title = ?, description = ?, deadline = ?, priority = ?, status = ?
            WHERE id = ? AND user_id = ?
        ');
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['deadline']    ?? null,
            $data['priority'],
            $data['status'],
            $id,
            $userId,
        ]);
        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Builds the WHERE clause and params array from filters
    private function buildWhere(int $userId, array $filters): array
    {
        $where  = ['t.user_id = ?'];
        $params = [$userId];

        if (!empty($filters['subject_id'])) {
            $where[]  = 't.subject_id = ?';
            $params[] = (int) $filters['subject_id'];
        }
        if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'completed'])) {
            $where[]  = 't.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['priority']) && in_array($filters['priority'], ['low', 'medium', 'high'])) {
            $where[]  = 't.priority = ?';
            $params[] = $filters['priority'];
        }

        return [implode(' AND ', $where), $params];
    }
}