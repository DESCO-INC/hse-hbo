<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = ['user', 'action', 'model', 'model_id', 'changes'];
}
