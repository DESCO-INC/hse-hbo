<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Organization extends Model
{
    use Auditable;
    use HasFactory;
    protected $table = 'organizations';
    protected $guarded = [];

    public function Hbolist()
    {
        return $this->hasMany(Hbolist::class);
    }
}
