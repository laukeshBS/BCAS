<?php

namespace App\Models\Admin;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Document extends Model
{
    
    protected $connection = 'mysql_admin';
    
    protected $fillable = [
        'document_category_id', 'doc_name', 'description', 'doc_type', 'doc', 'status', 'position'
    ];

}
