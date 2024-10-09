<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpsSecurity extends Model
{
    use SoftDeletes;

    protected $fillable = ['application_id','airport_name','entity_name','resion_name','cso_acso_name','cso_acso_email','cso_acso_mobile','station_name','date_of_approval','status','division','sec_type','date_of_validity','lang_code','created_by'
    ];
}
