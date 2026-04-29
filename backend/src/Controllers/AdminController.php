<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

class AdminController
{
    private UserRepository $userRepository;
    private array          $user;

    public function __construct(array $user)
    {
        $this->user           = $user;
        $this->userRepository = new UserRepository();
    }

    public function users(): void
    {
        $page   = max(1, (int) ($_GET['page']  ?? 1));
        $limit  = min(50, max(1, (int) ($_GET['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;

        $total = $this->userRepository->countAll();
        $users = $this->userRepository->findAll($limit, $offset);

        echo json_encode([
            'data' => $users,
            'meta' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => (int) ceil($total / $limit),
            ],
        ]);
    }
}