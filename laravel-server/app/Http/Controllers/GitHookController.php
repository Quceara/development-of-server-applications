<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use App\Http\Requests\SecretKeyRequest;

class GitHookController extends Controller
{
    protected static $isUpdating = false;

    public function updateProject(SecretKeyRequest $request)
    {
        $secretKey = env('SECRET_KEY');
        $requestKey = $request->input('secret_key');

        if (!$secretKey || $secretKey != $requestKey) {
            return response()->json(['message' => 'Ошибка: неверный секретный ключ.'], 403);
        }

        if (self::$isUpdating) {
            return response()->json(['message' => 'Обновление уже выполняется, подождите завершения.'], 429);
        }

        self::$isUpdating = true;

        try {
            $ip = $request->ip();
            $date = now();
            Log::info("Git update triggered by IP: {$ip} at {$date}");

            $this->runGitCommands();

            return response()->json(['message' => 'Project has been successfully updated.'], 200);
        } catch (\Exception $e) {
            Log::error('Error updating: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating the project.'], 500);
        } finally {
            self::$isUpdating = false;
        }
    }

    private function runGitCommands()
    {
        $this->runCommand(['git', 'checkout', 'main'], 'Switching to the main branch');

        $this->runCommand(['git', 'reset', '--hard'], 'Canceling local changes');

        $this->runCommand(['git', 'pull'], 'Updating a project with Git');
    }

    private function runCommand(array $command, $logMessage)
    {
        Log::info($logMessage);
        $process = new Process($command, base_path());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        Log::info($process->getOutput());
    }
}
