<?php

namespace App\Http\Controllers;

use App\DTO\ServerInfoDTO;
use App\DTO\ClientInfoDTO;
use App\DTO\DatabaseInfoDTO;
use Illuminate\Http\JsonResponse;

class InfoController extends Controller {
    public function serverInfo(): JsonResponse {
        $dto = new ServerInfoDTO(phpversion());

        return response()->json($dto);
    }

    public function clientInfo(): JsonResponse {
        $dto = new ClientInfoDTO(
	    request()->ip(),
            request()->header('User-Agent')
	);

        return response()->json($dto);
    }

    public function databaseInfo(): JsonResponse {
        $dto = new DatabaseInfoDTO(
            config('database.default'),
            config('database.connections.mysql.host')
        );

        return response()->json($dto);
    }
}
