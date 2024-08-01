<?php

namespace App\Models\Cms\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division_document extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','description','document','status','division','position','lang_code','start_date','end_date','is_news','category_id','created_by'];
    public function category()
    {
        return $this->belongsTo(Division_document_category::class, 'category_id');
    }

    
}
