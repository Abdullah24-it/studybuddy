<?php

namespace App\Controllers;

use App\Services\TaskService;
use App\Repositories\TaskRepository;
use App\Repositories\SubjectRepository;

class TaskController
{
    private TaskService $taskService;
    private array       $user;

    public function __construct(array $user)
    {
        $this->user        = $user;
        $this->taskService = new TaskService(
            new TaskRepository(),
            new SubjectRepository()
        );
    }

    public function index(): void
    {
        $page    = max(1, (int) ($_GET['page']  ?? 1));
        $limit   = min(50, max(1, (int) ($_GET['limit'] ?? 10)));
        $filters = [
            'subject_id' => $_GET['subject_id'] ?? null,
            'status'     => $_GET['status']     ?? null,
            'priority'   => $_GET['priority']   ?? null,
        ];

        $result = $this->taskService->getAll($this->user, $filters, $page, $limit);
        echo json_encode($result);
    }

    public function show(int $id): void
    {
        try {
            $task = $this->taskService->getById($id, $this->user);
            echo json_encode($task);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $task = $this->taskService->create($data ?? [], $this->user);
            http_response_code(201);
            echo json_encode($task);
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            http_response_code(403);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $task = $this->taskService->update($id, $data ?? [], $this->user);
            echo json_encode($task);
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->taskService->delete($id, $this->user);
            http_response_code(204);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}