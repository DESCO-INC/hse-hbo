<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class HboList extends Model
{
    use Auditable;
    use HasFactory;
    protected $table = 'hbo_list';
    protected $guarded = [];

    public function Organization()
    {
        return $this->hasMany(Organization::class);
    }
}
