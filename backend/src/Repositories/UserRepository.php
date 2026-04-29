<?php

namespace App\Repositories;

use App\Database;
use App\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findAll(int $limit, int $offset): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT id, name, email, role, created_at FROM users
             ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit',  $limit,  \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countAll(): int
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) FROM users');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function emailExists(string $email): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function create(string $name, string $email, string $hashedPassword, string $role): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, $hashedPassword, $role]);

        return [
            'id'    => (int) $db->lastInsertId(),
            'name'  => $name,
            'email' => $email,
            'role'  => $role,
        ];
    }
}