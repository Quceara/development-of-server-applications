<?php

namespace App;

use Illuminate\Support\Facades\Auth;

trait CreatedByAndDeletedByTrait
{
    protected static function bootTracksCreatedByAndDeletedBy()
    {
        static::creating(function ($model) {
            $model->created_by = request()->attributes->get('userId');
        });

        static::deleting(function ($model) {
            if (!$model->isForceDeleting()) {
                $model->deleted_by = request()->attributes->get('userId');
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }
}
