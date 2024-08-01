@extends('admin.layouts.master')

@section('title')
Menu Create - Admin Panel
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
<script type="text/javascript">


   	
	function addmenutype(id) {
       
		if(id=='1')
		{ 	
			document.getElementById('txtDoc').style.display = 'block';
			document.getElementById('txtPDF').style.display = "none";
			document.getElementById('txtweb').style.display = "none";
		}
		else if(id=='2')
		{	
			document.getElementById('txtDoc').style.display = 'none';
			document.getElementById('txtPDF').style.display = 'block';
			document.getElementById('txtweb').style.display = 'none';
			// document.getElementById('media').style.display = 'none';
		}
		else if(id=='3')
		{	
			document.getElementById('txtDoc').style.display = 'none';
			document.getElementById('txtPDF').style.display = 'none';
			document.getElementById('txtweb').style.display = 'block';
		}
		else 
		{	
			document.getElementById('txtDoc').style.display = 'none';
			document.getElementById('txtPDF').style.display = 'none';
			document.getElementById('txtweb').style.display = 'none';
		}	
	}
    
</script>
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Menu Create</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.menus.index') }}">All Menus</a></li>
                    <li><span>Create Menu </span></li>
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
                <form  action="{{URL::to('admin/menus/')}}" name="form1" id="form1" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    @csrf
                   
                    <div class="panel-body">
                    <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Menu  Language:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="input_class form-group">
                                <?php
                                    $language = get_language();
                                    foreach($language as $value) {
                                    ?>
                                    <input type="radio" name="language" autocomplete="off" id="txtlanguage" onclick="getMenu(this.value);" value="{{$value->lang_code}}"  @if(old('language') == $value->lang_code) checked @endif class="@error('language') is-invalid @enderror" />{{$value->name}} &nbsp;
                                    <?php } ?>

                                     @if($errors->has('language'))
                                    <p class="text-danger">{{ $errors->first('language') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div id="content1"  style="display:isset(old('language'))? 'block' : 'none' ">
                        <?php  $language_id=old('language'); if(isset($language_id)): ?>
                                <?php if(!isset($m_flag_id)) $m_flag_id=''; ?>
                                <?php echo primarylink_menu($language_id, $m_flag_id) ?>
                            <?php endif; ?>
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
                                    <input name="url" autocomplete="off" type="text" maxlength="36"
                                    minlength="" 
                                    class="input_class form-control @error('url') is-invalid @enderror " id="txteMenu_title"
                                    value="{{old('url')}}" />
                                    @error('url')
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
                                    <label>Menu Title:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="menu_title"  maxlength="36"
                                    minlength="2"  autocomplete="off" type="text"  
                                    class="input_class form-control  @error('menu_title') is-invalid @enderror" id="txtename"   value="{{old('menu_title')}}"  />
                                    @error('menu_title')
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
                                    <label>Menu Type:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="input_class form-group">
                               
                                    <select name="menutype" id="menutype" class="form-control @error('menutype') is-invalid @enderror" autocomplete="off" onchange="addmenutype(this.value)">
                                    <option value="">Select</option>
                                        <?php 
                                        $menuTypeArray = array("1"=>" Content ","2"=>"File Upload","3"=>"Web Site Url");
                                        foreach($menuTypeArray as $key=>$value)
                                        {
                                        ?>
                                        <option value="{{$key}}"  @if(old('menutype')==$key) selected @endif ><?php echo $value; ?></option>
                                        <?php 
                                        }
                                        ?>
                                    </select>
                                    @if($errors->has('menutype'))
                                    <span class="text-danger">{{ $errors->first('menutype') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label> Short Description:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <textarea name="welcomedescription" maxlength="120" autocomplete="off" class="input_class form-control summernote-simple @error('welcomedescription') is-invalid @enderror">{{old('welcomedescription')}}</textarea>
                                @if($errors->has('welcomedescription'))
                                <span class="text-danger">{{ $errors->first('welcomedescription') }}</span>
                                @endif
                                </div>
                            </div>
                        </div>

                        <div id="txtDoc" style="display: none;">
                            <!-- <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Meta Keyword:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input name="metakeyword" maxlength="64" autocomplete="off" type="text" class="input_class form-control" id="metakeyword" value="{{old('metakeyword')}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Meta Description:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input name="metadescription" maxlength="150" autocomplete="off" type="text" class="input_class form-control" id="metadescription" value="{{old('metadescription')}}" />
                                    </div>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Description:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <textarea name="description" id="description" class="form-control summernote-simple " rows="3" aria-hidden="true"><?php echo old('description'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Image Upload:</label>
                                        <!-- <span class="star">*</span> -->
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                    <input type="file"  name="img_upload" class="input_class inline-block" id="img_upload" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="txtPDF" style="display: none;">
                            <div class="row" >
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Document Upload:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="file" onchange="onlytxtuploadimg_and_pdf(this);"  name="txtupload" class="input_class inline-block" id="txtimg_pdf" />
                                    </div>
                                    <span class="txtimg_pdf_error" style="color:red;"></span>
                                </div>
                            </div>
                        </div>
                        <div  id="txtweb" style="display: none;">
                            <div class="row" >
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Web Site Link:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="txtweblink" id="txtweblink" class="input_class form-control" autocomplete="off" placeholder="https://www.xyz.com" value="{{old('txtweblink')}}" />
                                    </div>
                                    @if($errors->has('txtweblink'))
                                    <p class="text-danger">{{ $errors->first('txtweblink') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Content Position:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <select name="txtposition" class="input_class form-control" id="txtposition" autocomplete="off">
                                        <option value=""> Select </option>
                                            <?php
                                            $statusArray = get_content_position();
                                            foreach($statusArray as $key=>$value) {
                                                ?>
                                                <option value="<?php echo $key; ?>" <?php if(old('txtposition')==$key) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php  }?>
                                    </select>
                                    @if($errors->has('txtposition'))
                                    <span class="text-danger">{{ $errors->first('txtposition') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Menu Status:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                <select name="txtstatus" class="input_class form-control" id="txtstatus" autocomplete="off">
                                    <option value=""> Select </option>
                                        <?php
                                        $statusArray = get_status();
                                        foreach($statusArray as $key=>$value) {
                                            ?>
                                            <option value="<?php echo $key; ?>" <?php if(old('txtstatus')==$key) echo "selected"; ?>><?php echo $value; ?></option>
                                        <?php  }?>
                                </select>
                                @if($errors->has('txtstatus'))
                                <span class="text-danger">{{ $errors->first('txtstatus') }}</span>
                                @endif
                                
                                </div>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Banner Image:</label>
                                       
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                    <input type="file"  name="banner_img" class="input_class inline-block" id="banner_img" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                            </div>
                            <div class="col-12 col-md-6 col-xm-6">
                                <div class="">
                               
                                    <input name="cmdsubmit" type="submit" class="btn btn-success" id="cmdsubmit" value="Submit" />&nbsp;
                                    <a href="{{ url('/admin/menus')}}" class="btn btn-primary" >Back</a>
                                  
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
<script type="text/javascript">

function getMenu(id) {
    // Set CSRF token for AJAX request
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Set up AJAX request headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // Set up AJAX request data
    var data = {
        id: id,
        get_primarylink_menu: 'get_primarylink_menu'
    };

    // URL to the PHP script
    var linkUrl = "{{ route('admin.get_primarylink_menu') }}";

    // Send AJAX request
    $.ajax({
        url: linkUrl,
        type: "POST",
        data: data,
        cache: false,
        success: function (response) {
            // Parse JSON response
            var responseObject = JSON.parse(response);
            // Hide the loading indicator
            $("#loading").hide();
            // Update the content
            $("#content1").html(responseObject.html);
            // Fade in the content
            $("#content1").fadeIn("slow");
        },
        error: function (xhr, status, error) {
            // Handle errors here
            console.error(xhr.responseText);
        }
    });
}


    $(document).ready(function() {

        var id=@if(!empty(old('menutype'))){{old('menutype')}} @else 0 @endif;
    
        if(id=='1')
            { 	
                jQuery('#txtDoc').css('display', 'block')
                jQuery('#txtDoc').css('txtPDF', 'none')
                jQuery('#txtweb').css('txtPDF', 'none')
                
            }
            else if(id=='2')
            {	
                document.getElementById('txtDoc').style.display = 'none';
                document.getElementById('txtPDF').style.display = 'block';
                document.getElementById('txtweb').style.display = 'none';
                //document.getElementById('media').style.display = 'none';
            }
            else if(id=='3')
            {	
                document.getElementById('txtDoc').style.display = 'none';
                document.getElementById('txtPDF').style.display = 'none';
                document.getElementById('txtweb').style.display = 'block';
            }
            else 
            {	
                jQuery('#txtDoc').css('display', 'none')
                jQuery('#txtDoc').css('txtPDF', 'none')
                jQuery('#txtweb').css('txtPDF', 'none')
            }
    });
</script>

@endsection