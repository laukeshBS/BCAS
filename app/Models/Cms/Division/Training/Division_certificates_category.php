<?php

namespace App\Models\Cms\Division\Training;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Division_certificates_category extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['title','slugs','description','status','position','division','lang_code','created_by'];
 
}
