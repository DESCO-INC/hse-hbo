<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PobRecords extends Model
{
    /** @use HasFactory<\Database\Factories\PobRecordsFactory> */
    use HasFactory;
    protected $table = 'pob_records';
    protected $guarded = [];
    protected $casts = [
        'attendance_data' => 'array',
        'date' => 'date',
    ];
}
