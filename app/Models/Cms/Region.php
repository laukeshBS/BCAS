<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','status','position',
    ];
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'region_id')->orderBy('positions');
    }
}
