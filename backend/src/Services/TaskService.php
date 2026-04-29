<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\Interfaces\SubjectRepositoryInterface;

class TaskService
{
    private TaskRepositoryInterface   $taskRepository;
    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(
        TaskRepositoryInterface    $taskRepository,
        SubjectRepositoryInterface $subjectRepository
    ) {
        $this->taskRepository    = $taskRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function getAll(array $user, array $filters, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $userId = (int) $user['user_id'];

        $total = $this->taskRepository->countByUser($userId, $filters);
        $tasks = $this->taskRepository->findAllByUser($userId, $filters, $limit, $offset);

        return [
            'data' => $tasks,
            'meta' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => (int) ceil($total / $limit),
            ],
        ];
    }

    public function getById(int $id, array $user): array
    {
        $task = $this->taskRepository->findByIdAndUser($id, (int) $user['user_id']);

        if (!$task) {
            throw new \RuntimeException('Task not found');
        }

        return $task;
    }

    public function create(array $data, array $user): array
    {
        if (empty($data['title']) || empty($data['subject_id'])) {
            throw new \InvalidArgumentException('Title and subject_id are required');
        }

        // Verify subject belongs to user
        $subject = $this->subjectRepository->findByIdAndUser(
            (int) $data['subject_id'],
            (int) $user['user_id']
        );

        if (!$subject) {
            throw new \RuntimeException('Subject not found or access denied');
        }

        $priority = $data['priority'] ?? 'medium';
        if (!in_array($priority, ['low', 'medium', 'high'])) {
            $priority = 'medium';
        }

        return $this->taskRepository->create([
            'user_id'     => (int) $user['user_id'],
            'subject_id'  => (int) $data['subject_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'deadline'    => $data['deadline']    ?? null,
            'priority'    => $priority,
        ]);
    }

    public function update(int $id, array $data, array $user): array
    {
        $existing = $this->taskRepository->findByIdAndUser($id, (int) $user['user_id']);

        if (!$existing) {
            throw new \RuntimeException('Task not found');
        }

        $priority = $data['priority'] ?? 'medium';
        if (!in_array($priority, ['low', 'medium', 'high'])) {
            $priority = 'medium';
        }

        $status = $data['status'] ?? 'pending';
        if (!in_array($status, ['pending', 'completed'])) {
            $status = 'pending';
        }

        return $this->taskRepository->update($id, (int) $user['user_id'], [
            'title'       => $data['title']       ?? $existing['title'],
            'description' => $data['description'] ?? null,
            'deadline'    => $data['deadline']    ?? null,
            'priority'    => $priority,
            'status'      => $status,
        ]);
    }

    public function delete(int $id, array $user): void
    {
        $existing = $this->taskRepository->findByIdAndUser($id, (int) $user['user_id']);

        if (!$existing) {
            throw new \RuntimeException('Task not found');
        }

        $this->taskRepository->delete($id);
    }
}