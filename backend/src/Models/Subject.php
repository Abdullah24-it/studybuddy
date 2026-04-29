<?php

namespace App\Models;


class Subject
{
    public int     $id;
    public int     $user_id;
    public string  $name;
    public ?string $description;
    public string  $created_at;

    public function __construct(array $data)
    {
        $this->id          = (int) $data['id'];
        $this->user_id     = (int) $data['user_id'];
        $this->name        = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->created_at  = $data['created_at'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'name'        => $this->name,
            'description' => $this->description,
            'created_at'  => $this->created_at,
        ];
    }
}