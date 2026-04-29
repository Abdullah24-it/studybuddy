<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware {
    private static string $secret = 'your-super-secret-key-change-this';

    public static function authenticate(): array {
        $authHeader = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            $headers    = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!str_starts_with($authHeader, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(['error' => 'No token provided']);
            exit();
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key(self::$secret, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit();
        }
    }

    public static function requireRole(array $user, string $role): void {
        if (($user['role'] ?? '') !== $role) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden: insufficient permissions']);
            exit();
        }
    }

    public static function generateToken(array $payload): string {
        $payload['iat'] = time();
        $payload['exp'] = time() + (60 * 60 * 24 * 30);
        return JWT::encode($payload, self::$secret, 'HS256');
    }
}