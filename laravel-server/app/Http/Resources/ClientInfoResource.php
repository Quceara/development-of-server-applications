<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientInfoResource extends JsonResource {
    public function toArray($request) {
        return [
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
        ];
    }
}
