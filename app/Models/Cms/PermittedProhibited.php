<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermittedProhibited extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'images', 'description', 'carry_on','checked','status','positions','created_by','lang_code',
    ];
}
