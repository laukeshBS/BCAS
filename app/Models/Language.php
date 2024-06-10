<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    
    protected $fillable = [
        'name',
        'lang_code',
    ];

}
