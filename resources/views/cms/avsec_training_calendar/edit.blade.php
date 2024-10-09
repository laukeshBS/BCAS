@extends('admin.layouts.master')

@section('title')
Training Calendars Edit - Admin Panel
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
                <h4 class="page-title pull-left">Training Calendars Edit</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.training-calendars.index') }}">All Training Calendars</a></li>
                    <li><span>Edit Training Calendars</span></li>
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
                <form action="{{ route('admin.training-calendars.update', $data->id) }}" method="POST" enctype="multipart/form-data">
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
                                    foreach ($language as $value) {
                                    ?>
                                    <input type="radio" name="lang_code" value="{{ $value->lang_code }}"
                                           @if((!empty($data->lang_code) ? $data->lang_code : old('lang_code')) == $value->lang_code) checked @endif
                                           class="@error('lang_code') is-invalid @enderror" /> {{ $value->name }} &nbsp;
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
                                    <label>Training Name:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="avSec_training" type="text" class="input_class form-control @error('avSec_training') is-invalid @enderror"
                                           value="{{ old('avSec_training', $data->avSec_training) }}" required />
                                    @error('avSec_training')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>{{ $month }}:</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="{{ $month }}" type="text" class="input_class form-control @error($month) is-invalid @enderror"
                                           value="{{ old($month, $data->$month) }}" />
                                    @error($month)
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <select name="status" class="input_class form-control" required>
                                        <option value="">Select</option>
                                        <?php
                                        $statusArray = get_status();
                                        foreach ($statusArray as $key => $value) {
                                        ?>
                                        <option value="{{ $key }}" @if((!empty($data->status) ? $data->status : old('status')) == $key) selected @endif>
                                            {{ $value }}</option>
                                        <?php } ?>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Remarks:</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <textarea name="remarks" class="input_class form-control">{{ old('remarks', $data->remarks) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Positions:</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="positions" type="text" class="input_class form-control" value="{{ old('positions', $data->positions) }}" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3"></div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="">
                                    <input name="cmdsubmit" type="submit" class="btn btn-success" value="Submit" />
                                    <a href="{{ url('admin/training-calendars') }}" class="btn btn-primary">Back</a>
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
