<?php

namespace App\DTO;

class ServerInfoDTO {
    public string $phpVersion;

    public function __construct(string $phpVersion) {
        $this->phpVersion = $phpVersion;
    }
}
