<?php

namespace App\Models\Concerns;

trait BelongsToUser
{
    protected static function bootBelongsToUser(): void
    {
        static::creating(function ($model) {
            if (! $model->user_id && auth()->check()) {
                $model->user_id = auth()->id();
            }
        });
    }
}
