<?php

namespace App\Models;


class User
{
    public int    $id;
    public string $name;
    public string $email;
    public string $role;
    public string $created_at;

    public function __construct(array $data)
    {
        $this->id         = (int) $data['id'];
        $this->name       = $data['name'];
        $this->email      = $data['email'];
        $this->role       = $data['role'] ?? 'student';
        $this->created_at = $data['created_at'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role,
            'created_at' => $this->created_at,
        ];
    }
}