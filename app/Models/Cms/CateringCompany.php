<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CateringCompany extends Model
{
    use SoftDeletes;

    protected $fillable = ['regional_office','airport_name','entity_name','date_of_security_clearance','date_of_security_programme_approval','status','division','date_of_validity','lang_code','created_by'
    ];
}
