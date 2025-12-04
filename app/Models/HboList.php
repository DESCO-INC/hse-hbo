<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HboList extends Model
{
    use HasFactory;
    protected $table = 'hbo_list';
    protected $guarded = [];

    public function Organization()
    {
        return $this->hasMany(Organization::class);
    }
}
