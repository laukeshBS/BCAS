<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
       'fullname','email','phone','score','total','created_by','positions','status'
    ];
}
