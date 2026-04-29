<?php

namespace App;

use App\Controllers\AuthController;
use App\Controllers\SubjectController;
use App\Controllers\TaskController;
use App\Controllers\AdminController;
use App\Middleware\AuthMiddleware;

class Router {
    public function dispatch(): void {
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $uri = preg_replace('#^/api#', '', $uri);
        $uri = rtrim($uri, '/');
        if (empty($uri)) $uri = '/';

        $parts    = explode('/', ltrim($uri, '/'));
        $resource = $parts[0] ?? '';
        $id       = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
        $action   = $parts[1] ?? '';

        if ($resource === 'auth') {
            $controller = new AuthController();
            if ($action === 'register') { $controller->register(); return; }
            if ($action === 'login')    { $controller->login();    return; }
        }

        $user = AuthMiddleware::authenticate();

        if ($resource === 'subjects') {
            $controller = new SubjectController($user);
            match(true) {
                $method === 'GET'    && $id === null => $controller->index(),
                $method === 'GET'    && $id !== null => $controller->show($id),
                $method === 'POST'   && $id === null => $controller->store(),
                $method === 'PUT'    && $id !== null => $controller->update($id),
                $method === 'DELETE' && $id !== null => $controller->destroy($id),
                default => $this->notFound()
            };
            return;
        }

        if ($resource === 'tasks') {
            $controller = new TaskController($user);
            match(true) {
                $method === 'GET'    && $id === null => $controller->index(),
                $method === 'GET'    && $id !== null => $controller->show($id),
                $method === 'POST'   && $id === null => $controller->store(),
                $method === 'PUT'    && $id !== null => $controller->update($id),
                $method === 'DELETE' && $id !== null => $controller->destroy($id),
                default => $this->notFound()
            };
            return;
        }

        // Admin-only — role enforced in backend before anything runs
        if ($resource === 'admin') {
            AuthMiddleware::requireRole($user, 'admin');

            $controller = new AdminController($user);
            match(true) {
                $method === 'GET' && $action === 'users' => $controller->users(),
                default => $this->notFound()
            };
            return;
        }

        $this->notFound();
    }

    private function notFound(): void {
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}