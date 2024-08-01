<?php

namespace App\Models\CMS;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'title', 'description','status',
    ];

    public function slides()
    {
        return $this->hasMany(Slide::class, 'slider_id');
    }
}
