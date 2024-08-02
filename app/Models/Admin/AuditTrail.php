<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AuditTrail extends Model
{   use SoftDeletes;
    
    protected $connection = 'mysql_admin';
    
    use HasFactory;
    protected $fillable =['id','action_name','module_item_title','module_item_id','action_by','action_type','lang_id','old_data','new_data','approve_status','action_date','ip_address','action_by_role'
    ];
}
