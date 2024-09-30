<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airline extends Model
{
    use SoftDeletes;

    protected $fillable = ['application_id','entity_name','cso_acso_name','cso_acso_email','station_name','date_of_approval','status','air_type','date_of_validity','lang_code','created_by'
    ];
}
