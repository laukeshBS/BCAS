<?php

namespace App\Models\Admin;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Language extends Model
{
    
    protected $connection = 'mysql_admin';
    
    protected $fillable = [
        'name',
        'lang_code',
    ];

}
