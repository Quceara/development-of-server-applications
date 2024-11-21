<?php

namespace App\DTO;

class ChangeLogCollectionDTO
{
    public array $logs;

    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }
}
