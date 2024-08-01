<?php

namespace App\Models\Cms\Division\Training;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division_course extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','description','status','division','position','lang_code','start_date','end_date','center_id','is_news','category_id','created_by'];
    
    public function courseCategory()
    {
        return $this->belongsTo(Division_course_category::class);
    }

    public function center()
    {
        return $this->belongsTo(Division_register_centers::class);
    }
}
