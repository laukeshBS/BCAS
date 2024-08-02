<?php

namespace App\Models\Cms\Division\Training;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division_award extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['id','title','slugs','description','status','division','position','lang_code','start_date','end_date','is_news','category_id','created_by'];
    
 
}