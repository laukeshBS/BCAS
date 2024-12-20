<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'rank', 'phone','email','division_id','region_id','lang_code','status','type','positions',
    ];
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
