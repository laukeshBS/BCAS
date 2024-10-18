<?php

namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Menu extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['organization','roles','lang_code','positions','status'];
    
}
