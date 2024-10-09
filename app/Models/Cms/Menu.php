<?php

namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Menu extends Model
{   use SoftDeletes;
    use HasFactory;
    protected $fillable =['menu_type',
    'menu_child_id',
    'menu_position',
    'language_id',
    'menu_name',
    'menu_url',
    'menu_title',
    'menu_keyword',
    'menu_description',
    'content',
    'img_upload',
    'doc_upload',
    'menu_links',
    'start_date',
    'end_date',
    'create_date',
    'update_date',
    'approve_status',
    'created_by',
    'page_order',
    'current_version',
    'welcomedescription',
    'banner_img',
];
    public function children()
    {
        return $this->hasMany(Menu::class, 'menu_child_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'menu_child_id');
    }
}
