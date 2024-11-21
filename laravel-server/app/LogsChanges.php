<?php

namespace App;

use App\Models\ChangeLog;

trait LogsChanges
{
    protected static function bootLogsChanges()
    {
    	static::created(function ($model) {
            $model->logChangeCreated();
    	});

        static::updated(function ($model) {
            if ($model->isDirty('deleted_at')) {
                return;
            }
            $model->logChangeUpdated();
    	});

    	static::deleted(function ($model) {
            if ($model->trashed()) {
                $model->logChangeSoftDeleted();
            } else {
                $model->logChangeDeleted();
            }
    	});

    	static::restored(function ($model) {
            $model->logChangeRestored();
        });
    }

    protected function logChangeCreated(): void
    {
        $userId = $this->getUserId();

        ChangeLog::create([
            'entity_type' => strtolower(class_basename(self::class)),
            'entity_id' => $this->id,
            'old_values' => null,
            'new_values' => $this->toArray(),
            'user_id' => $userId,
            'action' => 'created',
        ]);
    }

    protected function logChangeUpdated(): void
    {
        $userId = $this->getUserId();

        $newValues = array_diff_key($this->getDirty(), array_flip($this->getHidden()));

        $oldValues = collect($newValues)->mapWithKeys(function ($newValue, $key) {
            return [$key => $this->getOriginal($key)];
        });

        $oldValues = array_diff_key($oldValues->toArray(), array_flip($this->getHidden()));

        ChangeLog::create([
            'entity_type' => strtolower(class_basename(self::class)),
            'entity_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId,
            'action' => 'updated',
        ]);
    }

    protected function logChangeDeleted(): void
    {
        $userId = $this->getUserId();

        $oldValues = $this->getOriginal();

        $oldValues = array_diff_key($oldValues, array_flip($this->getHidden()));

        ChangeLog::create([
            'entity_type' => strtolower(class_basename(self::class)),
            'entity_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => null,
            'user_id' => $userId,
            'action' => 'deleted',
        ]);
    }

    protected function getUserId(): int
    {
        return request()->attributes->get('userId') ?? $this->id;
    }

    protected function logChangeSoftDeleted(): void
    {
        $userId = $this->getUserId();
        $oldValues = ['deleted_by' => null];
        $newValues = ['deleted_by' => $userId];

        ChangeLog::create([
            'entity_type' => strtolower(class_basename(self::class)),
            'entity_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId,
            'action' => 'soft_deleted',
        ]);
    }

    protected function logChangeRestored(): void
    {
        $userId = $this->getUserId();

        $currentDeletedBy = self::withTrashed()->where('id', $this->id)->value('deleted_by');

        if ($currentDeletedBy === null) {
            return;
        }

        $oldValues = ['deleted_by' => $currentDeletedBy];
        $newValues = ['deleted_by' => null];

        ChangeLog::create([
            'entity_type' => strtolower(class_basename(self::class)),
            'entity_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId,
            'action' => 'restored',
        ]);
    }
}
