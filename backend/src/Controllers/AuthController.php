<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepository;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService(new UserRepository());
    }

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $user = $this->authService->register($data ?? []);
            http_response_code(201);
            echo json_encode($user);
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            http_response_code(409);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $result = $this->authService->login($data ?? []);
            echo json_encode($result);
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}