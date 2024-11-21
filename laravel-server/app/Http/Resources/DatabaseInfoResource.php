<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatabaseInfoResource extends JsonResource {
    public function toArray($request) {
        return [
            'database' => $this->database,
	    'database_host' => $this->databaseHost,
        ];
    }
}
