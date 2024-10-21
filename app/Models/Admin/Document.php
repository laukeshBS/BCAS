<?php

namespace App\Models\Admin;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    
    protected $connection = 'mysql_admin';
    
    protected $fillable = [
        'document_category_id', 'doc_name', 'description', 'doc_type', 'doc', 'status', 'position','start_date','end_date','submitted_by'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'document_role');
    }


}
