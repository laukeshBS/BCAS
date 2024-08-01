<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slide extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slider_id', 'title', 'description', 'url', 'media_type', 'media','lang_code', 'order_index', 'status',
    ];
    public function slider()
    {
        return $this->belongsTo(Slider::class, 'slider_id');
    }
}
