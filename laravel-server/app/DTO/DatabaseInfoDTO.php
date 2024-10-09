<?php

namespace App\DTO;

class DatabaseInfoDTO {
    public string $database;
    public string $databaseHost;

    public function __construct(string $database, string $databaseHost) {
        $this->database = $database;
        $this->databaseHost = $databaseHost;
    }
}
