<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            AuditTrail::create([
                'user' => Auth::user()->name ?? 'Guest',
                'action' => 'created',
                'model' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode($model->getAttributes()),
            ]);
        });

        static::updated(function ($model) {
            AuditTrail::create([
                'user' => Auth::user()->name ?? 'Guest',
                'action' => 'updated',
                'model' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode([
                    'old' => $model->getOriginal(),
                    'new' => $model->getAttributes(),
                ]),
            ]);
        });

        static::deleted(function ($model) {
            AuditTrail::create([
                'user' => Auth::user()->name ?? 'Guest',
                'action' => 'deleted',
                'model' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode($model->getAttributes()),
            ]);
        });
    }
}
