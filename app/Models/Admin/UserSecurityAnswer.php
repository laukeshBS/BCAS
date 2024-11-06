<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSecurityAnswer extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_admin';

    protected $fillable = [
        'user_id', 'security_questions_id', 'answer'
    ];
}
