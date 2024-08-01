<?php

namespace App\Models\Cms\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Division_gallery_category extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','description','parent_id','image','status','position','division','lang_code','created_by'];
   
}
