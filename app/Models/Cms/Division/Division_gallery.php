<?php

namespace App\Models\Cms\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division_gallery extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','parent_id','description','image','status','division','position','lang_code','category_id','created_by'];
  
}
