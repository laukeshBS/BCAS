<?php

namespace App\Models\Admin;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rank extends Model
{
    
    protected $connection = 'mysql_admin';
    
    protected $fillable = [
        'name','type',
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'rank_has_document', 'rank_id', 'document_id');
    }
}
