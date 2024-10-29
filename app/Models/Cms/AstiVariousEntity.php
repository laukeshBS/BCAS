<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AstiVariousEntity extends Model
{
    use SoftDeletes;

    protected $fillable = ['region_name','e_file_no','asti_name','asti_location','date_of_approval','in_principle_provisional','bcas_date_of_approval','approved_by_bcas','letter_no','lang_code','created_by'
    ];
}
