<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvsecTrainingCalendar extends Model
{
    use SoftDeletes;

    protected $fillable = ['avSec_training','January','February','March','April','May','June','July','August','September','October','November','December','status','remarks','positions','lang_code','created_by'
    ];
}
