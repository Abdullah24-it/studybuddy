<?php

namespace App\Controllers;

use App\Services\SubjectService;
use App\Repositories\SubjectRepository;

class SubjectController
{
    private SubjectService $subjectService;
    private array          $user;

    public function __construct(array $user)
    {
        $this->user           = $user;
        $this->subjectService = new SubjectService(new SubjectRepository());
    }

    public function index(): void
    {
        $page  = max(1, (int) ($_GET['page']  ?? 1));
        $limit = min(50, max(1, (int) ($_GET['limit'] ?? 10)));

        $result = $this->subjectService->getAll($this->user, $page, $limit);
        echo json_encode($result);
    }

    public function show(int $id): void
    {
        try {
            $subject = $this->subjectService->getById($id, $this->user);
            echo json_encode($subject);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $subject = $this->subjectService->create($data ?? [], $this->user);
            http_response_code(201);
            echo json_encode($subject);
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $subject = $this->subjectService->update($id, $data ?? [], $this->user);
            echo json_encode($subject);
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
            $this->subjectService->delete($id, $this->user);
            http_response_code(204);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}