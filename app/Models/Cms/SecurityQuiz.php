<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecurityQuiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
       'question','A','B','C','D','answer','quizs_type','lang_code','positions','status'
    ];
}
