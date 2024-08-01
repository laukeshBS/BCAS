@extends('admin.layouts.master')

@section('title')
CommonTitle Edit - Admin Panel
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
                <h4 class="page-title pull-left">CommonTitle Edit</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.common_title.index') }}">All CommonTitles</a></li>
                    <li><span>Edit CommonTitle </span></li>
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
                <form action="{{ route('admin.common_title.update' , $data->id) }}" name="form1" id="form1" method="post"
                    enctype="multipart/form-data" accept-charset="utf-8">
                    @csrf
                    @method('PUT')

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
                                    <input type="radio" name="lang_code" autocomplete="off" id="lang_code"
                                        onclick="getCommonTitle(this.value,{{$data->id}})" value="{{$value->lang_code}}"
                                        @if((!empty($data->lang_code) ? $data->lang_code : old('lang_code')) == $value->lang_code) checked @endif
                                        class="@error('lang_code') is-invalid @enderror" /> {{$value->name}} &nbsp;
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
                                    <input name="title"  minlength="2"
                                       autocomplete="off" type="text"
                                        class="input_class form-control  @error('title') is-invalid @enderror"
                                        id="title"
                                        value="{{ !empty($data->title)?$data->title:old('title')}}" />
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
                                    <label>Slugs:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="slugs"  minlength="2"
                                       autocomplete="off" type="text"
                                        class="input_class form-control  @error('slugs') is-invalid @enderror"
                                        id="slugs"
                                        value="{{ !empty($data->slugs)?$data->slugs:old('slugs')}}" />
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
                                    <label>Status:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <select name="status" class="input_class form-control" id="status"
                                        autocomplete="off">
                                        <option value=""> Select </option>
                                        <?php
                                        $statusArray = get_status();
                                        foreach($statusArray as $key=>$value) {
                                            ?>
                                        <option value="<?php echo $key; ?>"
                                            <?php if((!empty($data->status)?$data->status:old('status'))==$key) echo "selected"; ?>>
                                            <?php echo $value; ?></option>
                                        <?php  }?>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                        <div class="col-12 col-md-3 col-lg-3">
                                            </div>
                            <div class="col-12 col-md-6 col-xm-6">
                                <div class="">

                                    <input name="cmdsubmit" type="submit" class="btn btn-success" id="cmdsubmit"
                                        value="Submit" />&nbsp;
                                    <a href="{{ url('admin/common_title')}}" class="btn btn-primary">Back</a>
                                    <input type="hidden" name="random" value="" />
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