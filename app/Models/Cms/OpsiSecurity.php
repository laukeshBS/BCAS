<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpsiSecurity extends Model
{
    use SoftDeletes;

    protected $fillable = ['application_id','company_name','date_of_application_submitted','date_of_approval','status','positions','division','created_by','sec_type','date_of_validity','lang_code','created_by'
    ];
}
