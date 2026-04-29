<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Middleware\AuthMiddleware;

class AuthService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new \InvalidArgumentException('Name, email and password are required');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        if ($this->userRepository->emailExists($data['email'])) {
            throw new \RuntimeException('Email already registered');
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $role           = $data['role'] ?? 'student';

        return $this->userRepository->create(
            $data['name'],
            $data['email'],
            $hashedPassword,
            $role
        );
    }

    public function login(array $data): array
    {
        if (empty($data['email']) || empty($data['password'])) {
            throw new \InvalidArgumentException('Email and password are required');
        }

        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            throw new \RuntimeException('Invalid credentials');
        }

        $token = AuthMiddleware::generateToken([
            'user_id' => $user['id'],
            'email'   => $user['email'],
            'role'    => $user['role'],
        ]);

        return [
            'token' => $token,
            'user'  => [
                'id'    => (int) $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ],
        ];
    }
}