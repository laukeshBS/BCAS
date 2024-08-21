<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','status','lang_code','phone','email','address','fax','epabx',
    ];
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'division_id');
    }
}
