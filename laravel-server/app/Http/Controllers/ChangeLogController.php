<?php
namespace App\Http\Controllers;

use App\Models\ChangeLog;
use Illuminate\Http\Request;

class ChangeLogController extends Controller
{
    public function getEntityLogs(string $entity, int $id)
    {
        $logs = ChangeLog::where('entity_type', $entity)
            ->where('entity_id', $id)
            ->where('action', 'updated')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    public function restoreEntityLog(string $entity, int $id, int $mutationId)
    {
        $log = ChangeLog::where('entity_type', $entity)
            ->where('entity_id', $id)
            ->where('action', 'updated')
            ->where('id', $mutationId)
            ->first();

        if (!$log) {
            return response()->json(['message' => 'No log found for the specified mutation'], 404);
        }

        $oldValues = $log->old_values;

        $model = $this->getModelForEntity($entity, $id);

        if ($model) {
            foreach ($oldValues as $key => $value) {
                $model->$key = $value;
            }

            $model->save();
        }

        return response()->json(['message' => 'Entity restored successfully']);
    }

    private function getModelForEntity(string $entity, int $id)
    {
        $modelClass = 'App\\Models\\' . ucfirst(strtolower($entity));

        if (class_exists($modelClass)) {
            return $modelClass::find($id);
        }

        return null;
    }
}

