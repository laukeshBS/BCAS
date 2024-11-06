<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecurityQuestion extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_admin';
    
    protected $fillable = [
       'question','status'
    ];
}
