<?php

namespace App\Models\Cms\Common;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CommonTitle extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','status','lang_code','created_by'];
   
  
}
