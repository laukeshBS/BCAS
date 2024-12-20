<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'title', 'description','status',
    ];

    public function slides($langCode = null)
    {
        $query = $this->hasMany(Slide::class, 'slider_id');

        if ($langCode) {
            $query->where('lang_code', $langCode);
        }

        return $query;
    }
}
