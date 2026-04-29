<?php

namespace App\Services;

use App\Interfaces\SubjectRepositoryInterface;

class SubjectService
{
    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(SubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    public function getAll(array $user, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        if ($user['role'] === 'admin') {
            $total    = $this->subjectRepository->countAll();
            $subjects = $this->subjectRepository->findAll($limit, $offset);
        } else {
            $total    = $this->subjectRepository->countByUser((int) $user['user_id']);
            $subjects = $this->subjectRepository->findAllByUser((int) $user['user_id'], $limit, $offset);
        }

        return [
            'data' => $subjects,
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
        $subject = $this->subjectRepository->findByIdAndUser($id, (int) $user['user_id']);

        if (!$subject) {
            throw new \RuntimeException('Subject not found');
        }

        return $subject;
    }

    public function create(array $data, array $user): array
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Subject name is required');
        }

        return $this->subjectRepository->create(
            (int) $user['user_id'],
            $data['name'],
            $data['description'] ?? null
        );
    }

    public function update(int $id, array $data, array $user): array
    {
        $existing = $this->subjectRepository->findByIdAndUser($id, (int) $user['user_id']);

        if (!$existing) {
            throw new \RuntimeException('Subject not found');
        }

        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Subject name is required');
        }

        return $this->subjectRepository->update(
            $id,
            (int) $user['user_id'],
            $data['name'],
            $data['description'] ?? null
        );
    }

    public function delete(int $id, array $user): void
    {
        $existing = $this->subjectRepository->findByIdAndUser($id, (int) $user['user_id']);

        if (!$existing) {
            throw new \RuntimeException('Subject not found');
        }

        $this->subjectRepository->delete($id);
    }
}