<?php

namespace App\Models;


class Task
{
    public int     $id;
    public int     $user_id;
    public int     $subject_id;
    public string  $title;
    public ?string $description;
    public ?string $deadline;
    public string  $priority;
    public string  $status;
    public string  $created_at;

    public function __construct(array $data)
    {
        $this->id          = (int) $data['id'];
        $this->user_id     = (int) $data['user_id'];
        $this->subject_id  = (int) $data['subject_id'];
        $this->title       = $data['title'];
        $this->description = $data['description'] ?? null;
        $this->deadline    = $data['deadline']    ?? null;
        $this->priority    = $data['priority']    ?? 'medium';
        $this->status      = $data['status']      ?? 'pending';
        $this->created_at  = $data['created_at']  ?? '';
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'subject_id'  => $this->subject_id,
            'title'       => $this->title,
            'description' => $this->description,
            'deadline'    => $this->deadline,
            'priority'    => $this->priority,
            'status'      => $this->status,
            'created_at'  => $this->created_at,
        ];
    }
}