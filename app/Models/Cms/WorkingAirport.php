<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingAirport extends Model
{
    use SoftDeletes;

    protected $fillable = ['airport_orders','region_name','lang_code','sr_no','airport_name','entity_name','address','mobile_no','phone_no','unique_reference_number','approved_status_clearance','date_of_approval_clearance','approved_status_programme','date_of_approval_programme','valid_till'
    ];
}
