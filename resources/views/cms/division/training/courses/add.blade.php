@extends('admin.layouts.master')

@section('title')
Course Create - Admin Panel
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Course Create</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.division.training.course.index') }}">All Course </a></li>
                    <li><span>Create Course </span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('admin.layouts.partials.logout')
        </div>
    </div>
</div>
<div class="row">

    <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
            <div class="card-body"> 
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif 
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form  action="{{URL::to('/admin/division/training/course')}}" name="form1" id="form1" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    @csrf
                   
                    <div class="panel-body">
                    <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Language:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="input_class form-group">
                                <?php
                                    $language = get_language();
                                    foreach($language as $value) {
                                    ?>
                                    <input type="radio" name="lang_code" autocomplete="off" id="txtlanguage" onclick="getCourse(this.value);" value="{{$value->lang_code}}"  @if(old('language') == $value->lang_code) checked @endif class="@error('lang_code') is-invalid @enderror" />{{$value->name}} &nbsp;
                                    <?php } ?>

                                     @if($errors->has('lang_code'))
                                    <p class="text-danger">{{ $errors->first('lang_code') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                      
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Title:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="title"  maxlength="36"
                                    minlength="2"  autocomplete="off" type="text"  
                                    class="input_class form-control  @error('title') is-invalid @enderror" id="txtename"   value="{{old('title')}}"  />
                                    @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                       
                    
                        
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Description:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <textarea name="description" maxlength="120" autocomplete="off" class="input_class form-control @error('welcomedescription') is-invalid @enderror  summernote-simple">{{old('welcomedescription')}}</textarea>
                                @if($errors->has('description'))
                                <span class="text-danger">{{ $errors->first('description') }}</span>
                                @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Start date:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                        <input name="start_date"  maxlength="3"
                                        minlength="0"  autocomplete="off" type="date"  
                                        class="input_class form-control  @error('start_date') is-invalid @enderror" id="txtename"   value="{{old('start_date')}}"  />
                                        @error('start_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>End date:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                        <input name="end_date"  maxlength="3"
                                        minlength="0"  autocomplete="off" type="date"  
                                        class="input_class form-control  @error('end_date') is-invalid @enderror" id="txtename"   value="{{old('end_date')}}"  />
                                        @error('end_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Center:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                <select name="center_id" class="input_class form-control" id="center_id" autocomplete="off">
                                    <option value=""> Select </option>
                                        <?php
                                        $statusArray = get_register_center();
                                        foreach($statusArray as $value) {
                                            ?>
                                            <option value="<?php echo $value->id; ?>" <?php if(old('center_id')==$value->id) echo "selected"; ?>><?php echo $value->title; ?></option>
                                        <?php  }?>
                                </select>
                                @if($errors->has('center_id'))
                                <span class="text-danger">{{ $errors->first('center_id') }}</span>
                                @endif
                                
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Category:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                <select name="category_id" class="input_class form-control" id="category_id" autocomplete="off">
                                    <option value=""> Select </option>
                                        <?php
                                        $statusArray = get_course_categories();
                                        foreach($statusArray as $value) {
                                            ?>
                                            <option value="<?php echo $value->id; ?>" <?php if(old('category_id')==$value->id) echo "selected"; ?>><?php echo $value->title; ?></option>
                                        <?php  }?>
                                </select>
                                @if($errors->has('category_id'))
                                <span class="text-danger">{{ $errors->first('category_id') }}</span>
                                @endif
                                
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Position:</label>
                                    <!-- <span class="star">*</span> -->
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                        <input name="position"  maxlength="3"
                                        minlength="0"  autocomplete="off" type="number"  
                                        class="input_class form-control  @error('position') is-invalid @enderror" id="txtename"   value="{{old('Course_title')}}"  />
                                        @error('position')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                <select name="status" class="input_class form-control" id="status" autocomplete="off">
                                    <option value=""> Select </option>
                                        <?php
                                        $statusArray = get_status();
                                        foreach($statusArray as $key=>$value) {
                                            ?>
                                            <option value="<?php echo $key; ?>" <?php if(old('status')==$key) echo "selected"; ?>><?php echo $value; ?></option>
                                        <?php  }?>
                                </select>
                                @if($errors->has('status'))
                                <span class="text-danger">{{ $errors->first('status') }}</span>
                                @endif
                                
                                </div>
                            </div>
                        </div>
                      
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                            </div>
                            <div class="col-12 col-md-6 col-xm-6">
                                <div class="">
                               
                                    <input name="cmdsubmit" type="submit" class="btn btn-success" id="cmdsubmit" value="Submit" />&nbsp;
                                    <a href="{{ url('/admin/division/training/course')}}" class="btn btn-primary" >Back</a>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                
                </form>
               

            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('public/admin/assets/js/validate.js')}}"></script>


@endsection