<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerInfoResource extends JsonResource {
    public function toArray($request) {
        return [
            'php_version' => $this->phpVersion,
        ];
    }
}
