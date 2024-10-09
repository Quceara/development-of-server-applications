<?php

namespace App\Http\Controllers;

use App\DTO\ServerInfoDTO;
use App\DTO\ClientInfoDTO;
use App\DTO\DatabaseInfoDTO;
use Illuminate\Http\JsonResponse;

class InfoController extends Controller {
    public function serverInfo(): JsonResponse {
        // Создаем объект DTO с версией PHP через конструктор
        $dto = new ServerInfoDTO(phpversion());

        return response()->json($dto); // Возвращаем DTO как JSON
    }

    public function clientInfo(): JsonResponse {
        // Создаем объект DTO с IP-адресом и User-Agent через конструктор
        $dto = new ClientInfoDTO(request()->ip(), request()->header('User-Agent'));

        return response()->json($dto); // Возвращаем DTO как JSON
    }

    public function databaseInfo(): JsonResponse {
        // Создаем объект DTO с именем базы данных и IP через конструктор
        $dto = new DatabaseInfoDTO(
            config('database.default'), 
            config('database.connections.mysql.host')
        );

        return response()->json($dto); // Возвращаем DTO как JSON
    }
}
