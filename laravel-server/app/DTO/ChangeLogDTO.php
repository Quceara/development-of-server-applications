<?php

namespace App\DTO;

class ChangeLogDTO
{
    public string $entityType;
    public int $entityId;
    public ?array $oldValues;
    public ?array $newValues;
    public int $userId;
    public string $action;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(array $data)
    {
        $this->entityType = $data['entity_type'];
        $this->entityId = $data['entity_id'];
        $this->oldValues = $data['old_values'];
        $this->newValues = $data['new_values'];
        $this->userId = $data['user_id'];
        $this->action = $data['action'];
        $this->createdAt = $data['created_at'];
        $this->updatedAt = $data['updated_at'];
    }
}
