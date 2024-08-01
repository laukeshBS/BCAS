<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slider;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class HomepageController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('slider-list.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        $sliders = Slider::get();
        return view('backend.cms.slider.list', compact('sliders'));
    }
    public function add_slider()
    {
        if (is_null($this->user) || !$this->user->can('slider-add.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        }

        return view('Cms.slider.add');
    }
    public function store_slider()
    {
        
        $sliders = Slider::get();
        return view('Cms.slider.list', compact('sliders'));
    }
    public function edit_slider()
    {
        if (is_null($this->user) || !$this->user->can('slider-add.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        }

        return view('Cms.slider.add');
    }
    public function update_slider()
    {
        $sliders = Slider::get();
        return view('Cms.slider.list', compact('sliders'));
    }
}
