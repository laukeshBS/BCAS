<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    //use SoftDeletes;
    use HasFactory;

    protected $connection = 'mysql_admin';
    
    protected $fillable =['ip_address','user_agent','url','visited_at','lang_code'];
}
