<?php

namespace App\Models\Cms\Division\Training;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Division_register_centers extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','description','status','division','position','lang_code','address','url','created_by'];
    
    public function courses()
    {
        return $this->hasMany(Division_course::class, 'center_id');
    }
}
