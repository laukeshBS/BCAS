<?php

namespace App\Models\Cms\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DivisionDocumentCategory extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','description','status','position','division','lang_code','created_by'];
    public function documents()
    {
        return $this->hasMany(DivisionDocument::class, 'category_id');
    }
}
